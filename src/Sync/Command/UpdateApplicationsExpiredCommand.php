<?php

namespace App\Sync\Command;

use App\JobPosting\Entity\Application;
use App\JobPosting\Enum\ApplicationState;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpdateApplicationsExpiredCommand extends Command
{
    protected static $defaultName = 'app:sync:update-applications-expired';
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure(): void
    {
        $this->setDescription('Update applications expired');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $start = microtime(true);

        $io = new SymfonyStyle($input, $output);
        $io->title('App - Sync - Update applications expired');

        $applications = $this->em->getRepository(Application::class)->findInProgressWithUnpublishedJobPosting();

        $applicationsCount = \count($applications);
        if (0 === $applicationsCount) {
            $io->success('Nothing to update');

            return Command::SUCCESS;
        }

        $io->info([
            sprintf('Date: %s', date('Y-m-d H:i:s')),
            sprintf('%d applications to update', $applicationsCount),
        ]);

        if (false === $input->getOption('no-interaction') && false === $io->confirm('Update ?')) {
            return Command::SUCCESS;
        }

        $progressBar = new ProgressBar($output, $applicationsCount);

        foreach ($applications as $application) {
            /* @var Application $application */
            $application->setState(ApplicationState::EXPIRED);

            $progressBar->advance();
        }

        $this->em->flush();

        $progressBar->finish();
        $io->newLine(2);

        $io->success(sprintf('%d Applications updated', $applicationsCount));

        $end = microtime(true);
        $duration = $end - $start;

        $io->info(sprintf('Execution time: %.2f second(s)', $duration));

        return Command::SUCCESS;
    }
}
