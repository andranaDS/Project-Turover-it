<?php

namespace App\User\Command;

use App\Core\Mailer\Mailer;
use App\User\Email\ImmediateAvailabilityConfirmEmail;
use App\User\Entity\User;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImmediateAvailabilityConfirmCommand extends Command
{
    private const ALERT_NUMBER_DAYS = 15;
    protected static $defaultName = 'app:alert:immediate-availability-confirm';

    private EntityManagerInterface $em;
    private Mailer $mailer;
    private int $emailImmediateAvailabilityConfirmationTtl;

    public function __construct(EntityManagerInterface $em, Mailer $mailer, int $emailImmediateAvailabilityConfirmationTtl)
    {
        parent::__construct();
        $this->em = $em;
        $this->mailer = $mailer;
        $this->emailImmediateAvailabilityConfirmationTtl = $emailImmediateAvailabilityConfirmationTtl;
    }

    protected function configure(): void
    {
        $this->setDescription('Sends emails to users to confirm their immediate availability');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $EmailsSentCount = 0;
        $start = Carbon::today()->subDays(self::ALERT_NUMBER_DAYS);

        $users = $this->em->getRepository(User::class)->findAllVisibleWIthImmediateAvailabilityByDateToIterate(
            $start,
            $start->copy()->endOfDay()
        );

        /** @var User $user */
        foreach ($users as $user) {
            if (null === $user->getEmail()) {
                continue;
            }

            try {
                $email = (new ImmediateAvailabilityConfirmEmail())
                    ->to($user->getEmail())
                    ->context([
                        'user' => $user,
                        'urlSignedTll' => $this->emailImmediateAvailabilityConfirmationTtl,
                    ])
                ;

                $this->mailer->send($email);

                ++$EmailsSentCount;
            } catch (\Exception $e) {
                $io->error($e->getMessage());
            }

            $this->em->clear();
        }

        $io->success(sprintf(' %d alert(s) sent', $EmailsSentCount));

        return Command::SUCCESS;
    }
}
