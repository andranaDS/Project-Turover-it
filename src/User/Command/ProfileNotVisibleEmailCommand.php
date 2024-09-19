<?php

namespace App\User\Command;

use App\Core\Mailer\Mailer;
use App\User\Email\ProfileNotVisibleEmail;
use App\User\Entity\User;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ProfileNotVisibleEmailCommand extends Command
{
    private const STATUS_NUMBER_MONTH_INTERVALS = [
        [3, 6, null],
        [6, 9, 3],
    ];

    protected static $defaultName = 'app:alert:profile-not-visible';

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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $EmailsSentCount = 0;
        $now = Carbon::now();
        $today = Carbon::today();

        if (false === $launchDate = Carbon::createFromFormat('d/m/Y', $this->launchDate)) {
            $io->error(sprintf('Invalid launch date format for: %s', $this->launchDate));

            return Command::FAILURE;
        }

        $launchDate->startOfDay();
        foreach (self::STATUS_NUMBER_MONTH_INTERVALS as $interval) {
            $start = $today->copy()->subMonths($interval[1])->endOfDay();
            $end = $today->copy()->subMonths($interval[0])->endOfDay();

            $users = $this->em->getRepository(User::class)->findAllNonVisibleByStatusDateToIterate(
                $start,
                $end,
                $launchDate
            );

            /** @var User $user */
            foreach ($users as $user) {
                if (null !== $user->getData() && null !== $user->getData()->getCronProfileNotVisibleExecAt()) {
                    if (null === $user->getStatusUpdatedAt() || null === $interval[2]) {
                        continue;
                    }

                    $execAtLimit = Carbon::createFromTimestamp($user->getStatusUpdatedAt()->getTimestamp())->addMonths($interval[2])->endOfDay();

                    if ($user->getData()->getCronProfileNotVisibleExecAt() > $execAtLimit) {
                        continue;
                    }
                }

                if (null === $user->getEmail()) {
                    continue;
                }

                try {
                    $email = (new ProfileNotVisibleEmail())
                        ->to($user->getEmail())
                        ->context([
                            'user' => $user,
                        ])
                    ;

                    $this->mailer->send($email);

                    if (null !== $user->getData()) {
                        $user->getData()->setCronProfileNotVisibleExecAt($now);
                    }

                    ++$EmailsSentCount;
                } catch (\Exception $e) {
                    $io->error($e->getMessage());
                }

                $this->em->flush();
                $this->em->clear();
            }
        }

        $io->success(sprintf(' %d alert(s) sent', $EmailsSentCount));

        return Command::SUCCESS;
    }
}
