<?php

namespace App\User\Command;

use App\Core\Mailer\Mailer;
use App\User\Email\AlertUserWithoutJobPostingSearchEmail;
use App\User\Entity\User;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AlertUserWithoutJobPostingSearchCommand extends Command
{
    private const ALERT_NUMBER_DAYS = 3;
    protected static $defaultName = 'app:alert:user-without-job-posting-search';

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
        $this->setDescription('Send emails to users that did not set an alert yet');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $EmailsSentCount = 0;
        $now = Carbon::now();
        $start = Carbon::today()->subDays(self::ALERT_NUMBER_DAYS);

        if (false === $launchDate = Carbon::createFromFormat('d/m/Y', $this->launchDate)) {
            $io->error(sprintf('Invalid launch date format for: %s', $this->launchDate));

            return Command::FAILURE;
        }

        $launchDate->startOfDay();
        $users = $this->em->getRepository(User::class)->findAllWithNoJobPostingSearchByDateToIterate($start, $launchDate);

        /** @var User $user */
        foreach ($users as $user) {
            if (null === $user->getEmail()) {
                continue;
            }

            try {
                $email = (new AlertUserWithoutJobPostingSearchEmail())
                    ->to($user->getEmail())
                    ->context([
                        'user' => $user,
                    ])
                ;

                $this->mailer->send($email);

                if (null !== $user->getData()) {
                    $user->getData()->setCronNoJobPostingSearchExecAt($now);
                }

                ++$EmailsSentCount;
            } catch (\Exception $e) {
                $io->error($e->getMessage());
            }

            $this->em->flush();
            $this->em->clear();
        }

        $io->success(sprintf(' %d alert(s) sent', $EmailsSentCount));

        return Command::SUCCESS;
    }
}
