<?php

namespace App\User\Command;

use App\Core\Mailer\Mailer;
use App\User\Email\NoImmediateAvailabilityConfirmEmail;
use App\User\Entity\User;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class NoImmediateAvailabilityConfirmCommand extends Command
{
    private const AVAILABILITY_NUMBER_DAY_INTERVALS = [
        [7, 14, null],
        [1, 7, 7],
    ];
    private const STATUS_UPDATE_NUMBER_DAYS = 14;
    protected static $defaultName = 'app:alert:no-immediate-availability-confirm';

    private EntityManagerInterface $em;
    private Mailer $mailer;
    private int $emailNoImmediateAvailabilityConfirmationTtl;

    public function __construct(EntityManagerInterface $em, Mailer $mailer, int $emailNoImmediateAvailabilityConfirmationTtl)
    {
        parent::__construct();
        $this->em = $em;
        $this->mailer = $mailer;
        $this->emailNoImmediateAvailabilityConfirmationTtl = $emailNoImmediateAvailabilityConfirmationTtl;
    }

    protected function configure(): void
    {
        $this->setDescription('Sends emails to users to confirm their non-immediate availability');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $EmailsSentCount = 0;
        $now = Carbon::now();
        $today = Carbon::today();
        $statusUpdateDate = $today->copy()->subDays(self::STATUS_UPDATE_NUMBER_DAYS);

        foreach (self::AVAILABILITY_NUMBER_DAY_INTERVALS as $interval) {
            $start = $today->copy()->addDays($interval[0])->endOfDay();
            $end = $today->copy()->addDays($interval[1])->endOfDay();

            $users = $this->em->getRepository(User::class)->findAllVisibleWIthNoImmediateAvailabilityByDateToIterate(
                $start,
                $end,
                $statusUpdateDate
            );

            /** @var User $user */
            foreach ($users as $user) {
                if (null !== $user->getData() && null !== $user->getData()->getCronNoImmediateAvailabilityExecAt()) {
                    if (null === $user->getNextAvailabilityAt() || null === $interval[2]) {
                        continue;
                    }

                    $execAtLimit = Carbon::createFromTimestamp($user->getNextAvailabilityAt()->getTimestamp())->subDays($interval[2])->startOfDay();

                    if ($user->getData()->getCronNoImmediateAvailabilityExecAt() > $execAtLimit) {
                        continue;
                    }
                }

                if (null === $user->getEmail()) {
                    continue;
                }

                try {
                    $email = (new NoImmediateAvailabilityConfirmEmail())
                        ->to($user->getEmail())
                        ->context([
                            'user' => $user,
                            'urlSignedTll' => $this->emailNoImmediateAvailabilityConfirmationTtl,
                        ])
                    ;

                    $this->mailer->send($email);

                    if (null !== $user->getData()) {
                        $user->getData()->setCronNoImmediateAvailabilityExecAt($now);
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
