<?php

namespace App\Forum\Command;

use App\Core\Mailer\Mailer;
use App\Forum\Email\ForumStatisticsEmail;
use App\Forum\Manager\ForumStatisticsManager;
use Carbon\Carbon;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ForumStatisticsCommand extends Command
{
    protected static $defaultName = 'app:forum:statistics';
    private Mailer $mailer;
    private ForumStatisticsManager $forumStatisticsManager;
    private string $contactRecipientAdmin;

    public function __construct(ForumStatisticsManager $forumStatisticsManager, Mailer $mailer, string $contactRecipientAdmin)
    {
        parent::__construct();
        $this->mailer = $mailer;
        $this->forumStatisticsManager = $forumStatisticsManager;
        $this->contactRecipientAdmin = $contactRecipientAdmin;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $yesterday = Carbon::yesterday();
        $data = $this->forumStatisticsManager->getDataByDateInterval($yesterday, Carbon::today());

        try {
            $email = (new ForumStatisticsEmail())
                ->to($this->contactRecipientAdmin)
                ->context([
                    'data' => $data,
                    'date' => $yesterday,
                ])
            ;

            $this->mailer->send($email);

            $io->success(' Forum statistics daily email sent');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }
    }
}
