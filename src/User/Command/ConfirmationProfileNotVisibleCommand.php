<?php

namespace App\User\Command;

use App\Core\Mailer\Mailer;
use App\User\Email\ConfirmationProfileNotVisibleEmail;
use App\User\Entity\User;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ConfirmationProfileNotVisibleCommand extends Command
{
    protected static $defaultName = 'app:alert:confirmation-profile-not-visible';

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
        $start = Carbon::today()->subMonths(3)->subHours(72);

        $users = $this->em->getRepository(User::class)->findAllWithImmediateAvailabilityByLastActivityDateToIterate(
            $start,
            $start->copy()->endOfDay(),
            Carbon::today()->subMonths(3)->subHours(72),
            true
        );

        /** @var User $user */
        foreach ($users as $user) {
            if (null === $user->getEmail()) {
                continue;
            }

            $user->setVisible(false);

            $this->em->flush();

            try {
                $email = (new ConfirmationProfileNotVisibleEmail())
                    ->to($user->getEmail())
                    ->context([
                        'user' => $user,
                    ])
                ;

                $this->mailer->send($email);

                ++$EmailsSentCount;
            } catch (\Exception $e) {
                $io->error($e->getMessage());
            }
        }

        $io->success(sprintf(' %d alert(s) sent', $EmailsSentCount));

        return Command::SUCCESS;
    }
}
