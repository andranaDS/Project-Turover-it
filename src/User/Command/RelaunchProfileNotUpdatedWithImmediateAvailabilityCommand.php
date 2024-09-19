<?php

namespace App\User\Command;

use App\Core\Mailer\Mailer;
use App\User\Email\RelaunchProfileNotUpdatedWithImmediateAvailabilityEmail;
use App\User\Entity\User;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RelaunchProfileNotUpdatedWithImmediateAvailabilityCommand extends Command
{
    private const LAST_ACTIVITY_NUMBER_MONTH = 3;
    private const UPDATED_NUMBER_MONTH = 3;
    protected static $defaultName = 'app:alert:relaunch-profile-not-updated-immediate-availability';

    private EntityManagerInterface $em;
    private Mailer $mailer;

    public function __construct(EntityManagerInterface $em, Mailer $mailer)
    {
        parent::__construct();
        $this->em = $em;
        $this->mailer = $mailer;
    }

    protected function configure(): void
    {
        $this->setDescription('');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $EmailsSentCount = 0;
        $start = Carbon::today()->subMonths(self::LAST_ACTIVITY_NUMBER_MONTH);

        $users = $this->em->getRepository(User::class)->findAllWithImmediateAvailabilityByLastActivityDateToIterate(
            $start,
            $start->copy()->endOfDay(),
            Carbon::today()->subMonths(self::UPDATED_NUMBER_MONTH)
        );

        /** @var User $user */
        foreach ($users as $user) {
            if (null === $user->getEmail()) {
                continue;
            }

            try {
                $email = (new RelaunchProfileNotUpdatedWithImmediateAvailabilityEmail())
                    ->to($user->getEmail())
                    ->context([
                        'user' => $user,
                    ])
                ;

                $this->mailer->send($email);
            } catch (\Exception $e) {
                $io->error($e->getMessage());
            }

            ++$EmailsSentCount;
            $this->em->clear();
        }

        $io->success(sprintf(' %d alert(s) sent', $EmailsSentCount));

        return Command::SUCCESS;
    }
}
