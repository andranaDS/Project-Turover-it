<?php

namespace App\User\Command;

use App\Core\Mailer\Mailer;
use App\User\Email\AlertUserProfileUncompletedEmail;
use App\User\Entity\User;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AlertUserProfileUncompletedCommand extends Command
{
    private const ALERTS_NUMBER_DAY_INTERVALS = [
        [2, 7, null],
        [7, 30, 7],
        [30, 45, 30],
    ];
    protected static $defaultName = 'app:alert:user-profile-uncomplete';

    private EntityManagerInterface $em;
    private Mailer $mailer;
    private string $launchDate;

    public function __construct(EntityManagerInterface $em, Mailer $mailer, string $launchDate)
    {
        parent::__construct();
        $this->em = $em;
        $this->mailer = $mailer;
        $this->launchDate = $launchDate;
    }

    protected function configure(): void
    {
        $this->setDescription('Send emails to users that did not have a completed profile');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $emailsSentCount = 0;
        $now = Carbon::now();
        $today = Carbon::today();

        if (false === $launchDate = Carbon::createFromFormat('d/m/Y', $this->launchDate)) {
            $io->error(sprintf('Invalid launch date format for: %s', $this->launchDate));

            return Command::FAILURE;
        }

        $launchDate->startOfDay();
        foreach (self::ALERTS_NUMBER_DAY_INTERVALS as $interval) {
            $start = $today->copy()->subDays($interval[1])->endOfDay();
            $end = $today->copy()->subDays($interval[0])->endOfDay();

            $users = $this->em->getRepository(User::class)->findWithUncompletedProfileByDateToIterate(
                $start,
                $end,
                $launchDate
            );

            /** @var User $user */
            foreach ($users as $user) {
                if (null !== $user->getData() && null !== $user->getData()->getCronProfileUncompletedExecAt()) {
                    if (null === $user->getCreatedAt() || null === $interval[2]) {
                        continue;
                    }

                    $execAtLimit = Carbon::createFromTimestamp($user->getCreatedAt()->getTimestamp())->addDays($interval[2])->endOfDay();

                    if ($user->getData()->getCronProfileUncompletedExecAt() > $execAtLimit) {
                        continue;
                    }
                }

                if (null === $user->getEmail()) {
                    continue;
                }

                try {
                    $email = (new AlertUserProfileUncompletedEmail())
                        ->to($user->getEmail())
                        ->context([
                            'user' => $user,
                        ])
                    ;

                    $this->mailer->send($email);

                    if (null !== $user->getData()) {
                        $user->getData()->setCronProfileUncompletedExecAt($now);
                    }

                    ++$emailsSentCount;
                } catch (\Exception $e) {
                    $io->error($e->getMessage());
                }

                $this->em->flush();

                $this->em->clear();
            }
        }

        $io->success(sprintf('%d emails(s) sent', $emailsSentCount));

        return Command::SUCCESS;
    }
}
