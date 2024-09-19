<?php

namespace App\User\Command;

use App\Core\Mailer\Mailer;
use App\User\Email\UpdateAvailabilityWithNoticeEmail;
use App\User\Entity\User;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpdateAvailabilityWithNoticeCommand extends Command
{
    private const STATUS_UPDATE_NUMBER_DAYS = [45, 75];
    protected static $defaultName = 'app:alert:update-availability-with-notice';

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
        $today = Carbon::today();

        foreach (self::STATUS_UPDATE_NUMBER_DAYS as $nbDays) {
            $start = $today->copy()->subDays($nbDays);
            $users = $this->em->getRepository(User::class)->findAllVisibleWithNoAvailabilityByDateToIterate(
                $start,
                $start->copy()->endOfDay(),
            );

            /** @var User $user */
            foreach ($users as $user) {
                if (null === $user->getEmail()) {
                    continue;
                }

                try {
                    $email = (new UpdateAvailabilityWithNoticeEmail())
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

                $this->em->clear();
            }
        }

        $io->success(sprintf(' %d alert(s) sent', $EmailsSentCount));

        return Command::SUCCESS;
    }
}
