<?php

namespace App\User\Command;

use App\Core\Mailer\Mailer;
use App\Notification\Twig\Email;
use App\User\Email\ImmediateAvailabilityConfirmFirstRelaunchEmail;
use App\User\Email\ImmediateAvailabilitySecondRelaunchEmail;
use App\User\Entity\User;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImmediateAvailabilityConfirmRelaunchCommand extends Command
{
    private const ALERT_NUMBER_MONTH = [1, 2];
    protected static $defaultName = 'app:alert:immediate-availability-confirm-relaunch';

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
        $this
            ->setDescription('Sends emails to users to confirm their immediate availability after 1 and 2 month(s)')
            ->addArgument('relaunch-number', InputArgument::REQUIRED, 'the number of the relaunch')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $EmailsSentCount = 0;
        $relaunchNumber = (int) $input->getArgument('relaunch-number');

        if (false === isset(self::ALERT_NUMBER_MONTH[$relaunchNumber])) {
            $io->error(sprintf('%d is not a valid relaunch number', $relaunchNumber));

            return Command::FAILURE;
        }

        $start = Carbon::today()->subMonths(self::ALERT_NUMBER_MONTH[$relaunchNumber]);

        $users = $this->em->getRepository(User::class)->findAllVisibleWIthImmediateAvailabilityByDateToIterate(
            $start,
            $start->copy()->endOfDay()
        );

        /** @var User $user */
        foreach ($users as $user) {
            $email = null;
            if (null === $user->getEmail()) {
                continue;
            }

            try {
                if (0 === $relaunchNumber) {
                    $email = $this->getFirstRelaunchEmail($user);
                } elseif (1 === $relaunchNumber) {
                    $email = $this->getSecondRelaunchEmail($user);
                }

                if (null !== $email) {
                    $this->mailer->send($email);

                    ++$EmailsSentCount;
                }
            } catch (\Exception $e) {
                $io->error($e->getMessage());
            }

            $this->em->clear();
        }

        $io->success(sprintf(' %d email(s) sent', $EmailsSentCount));

        return Command::SUCCESS;
    }

    private function getFirstRelaunchEmail(User $user): Email
    {
        return (new ImmediateAvailabilityConfirmFirstRelaunchEmail())
            ->to((string) $user->getEmail())
            ->context([
                'user' => $user,
            ])
        ;
    }

    private function getSecondRelaunchEmail(User $user): Email
    {
        return (new ImmediateAvailabilitySecondRelaunchEmail())
            ->to((string) $user->getEmail())
            ->context([
                'user' => $user,
            ])
        ;
    }
}
