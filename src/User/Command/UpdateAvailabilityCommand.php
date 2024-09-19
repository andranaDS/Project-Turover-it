<?php

namespace App\User\Command;

use App\User\Entity\User;
use App\User\Enum\Availability;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpdateAvailabilityCommand extends Command
{
    private const UPDATE_DATE_START = '01/01/2022';
    protected static $defaultName = 'app:alert:update-availability';

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure(): void
    {
        $this->setDescription('Sends emails to users to confirm their non-immediate availability');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $today = Carbon::today();
        $UserUpdatedCount = 0;

        if (false === $dateStart = Carbon::createFromFormat('d/m/Y', self::UPDATE_DATE_START)) {
            $io->error(sprintf('Invalid date start format for: %s', self::UPDATE_DATE_START));

            return Command::FAILURE;
        }

        $users = $this->em->getRepository(User::class)->findAllNoImmediateAvailabilityByNextAvailabilityDateToIterate(
            $today,
            $dateStart
        );

        /** @var User $user */
        foreach ($users as $user) {
            $user
                ->setNextAvailabilityAt($today)
                ->setAvailability(Availability::IMMEDIATE)
            ;

            ++$UserUpdatedCount;
        }

        $this->em->flush();

        $io->success(sprintf(' %d user(s) updated', $UserUpdatedCount));

        return Command::SUCCESS;
    }
}
