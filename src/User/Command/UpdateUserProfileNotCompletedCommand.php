<?php

namespace App\User\Command;

use App\Core\Util\Arrays;
use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Webmozarts\Console\Parallelization\ContainerAwareCommand;
use Webmozarts\Console\Parallelization\Parallelization;
use Webmozarts\Console\Parallelization\ParallelizationInput;

class UpdateUserProfileNotCompletedCommand extends ContainerAwareCommand
{
    use Parallelization;

    protected static $defaultName = 'app:user:update-profile-not-completed';
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure(): void
    {
        $this->setDescription('Set user profile to not complete if inactive');

        self::configureParallelization($this);
    }

    protected function fetchItems(InputInterface $input): array
    {
        return Arrays::map(
            $this->em->getRepository(User::class)->findAllToSetProfileNotCompleted(),
            static function (User $user) {
                return $user->getEmail();
            });
    }

    protected function getItemName(int $count): string
    {
        return 1 === $count ? 'user' : 'users';
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $parallelizationInput = new ParallelizationInput($input);

        $start = microtime(true);

        if ($parallelizationInput->isChildProcess()) {
            $this->executeChildProcess($input, $output);

            return Command::SUCCESS;
        }

        $this->executeMasterProcess($parallelizationInput, $input, $output);

        $end = microtime(true);
        $duration = $end - $start;
        $io->info(sprintf('Execution time: %.2f second(s)', $duration));

        return Command::SUCCESS;
    }

    protected function runAfterBatch(InputInterface $input, OutputInterface $output, array $items): void
    {
        $this->em->flush();
    }

    protected function runSingleCommand(string $email, InputInterface $input, OutputInterface $output): void
    {
        /** @var User $user */
        $user = $this->em->getRepository(User::class)->findOneByEmail($email);

        $user
            ->setProfileCompleted(false)
            ->setFormStep(null)
        ;
    }

    protected function getBatchSize(): int
    {
        return 64;
    }

    protected function getSegmentSize(): int
    {
        return 64;
    }
}
