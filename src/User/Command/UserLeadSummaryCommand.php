<?php

namespace App\User\Command;

use App\Core\Mailer\Mailer;
use App\User\Email\UserLeadsSummaryEmail;
use App\User\Entity\UserLead;
use App\User\Repository\UserLeadRepository;
use Carbon\Carbon;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UserLeadSummaryCommand extends Command
{
    protected static $defaultName = 'app:user:leads-email-summary';
    private Mailer $mailer;
    private string $contactRecipientAdmin;
    private UserLeadRepository $userLeadRepository;

    public function __construct(UserLeadRepository $userLeadRepository, Mailer $mailer, string $contactRecipientAdmin)
    {
        parent::__construct();
        $this->mailer = $mailer;
        $this->contactRecipientAdmin = $contactRecipientAdmin;
        $this->userLeadRepository = $userLeadRepository;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $yesterday = Carbon::yesterday();
        $userLeads = $this->userLeadRepository->getByDateInterval($yesterday, Carbon::today());

        if (false === $name = tempnam(sys_get_temp_dir(), 'csv')) {
            $io->error('A problem occurred during the creation of the file');

            return Command::FAILURE;
        }

        if (false === $fp = fopen($name, 'w')) {
            $io->error('A problem occurred while opening the file.');

            return Command::FAILURE;
        }

        /** @var UserLead $userLead */
        foreach ($userLeads as $userLead) {
            $line = array_merge([$userLead->getCreatedAt()?->format('d/m/Y h:i')], array_values($userLead->getContent()));
            fputcsv($fp, $line);
        }

        fclose($fp);

        try {
            $email = (new UserLeadsSummaryEmail())
                ->to($this->contactRecipientAdmin)
                ->attach(
                    (string) file_get_contents($name), 'recap_.csv', 'application/csv')
                ->context([
                    'date' => $yesterday,
                ])
            ;

            $this->mailer->send($email);

            $io->success('User leads daily email sent');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }
    }
}
