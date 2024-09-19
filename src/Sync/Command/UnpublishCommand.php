<?php

namespace App\Sync\Command;

use App\Core\Util\Arrays;
use App\JobPosting\Entity\JobPosting;
use App\Sync\Entity\SyncLog;
use App\Sync\Enum\SyncLogMode;
use App\Sync\Enum\SyncLogSource;
use App\Sync\Turnover\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UnpublishCommand extends Command
{
    protected static $defaultName = 'app:sync:unpublish';
    private EntityManagerInterface $em;
    private Client $turnover;

    public function __construct(EntityManagerInterface $em, Client $turnover)
    {
        parent::__construct();

        $this->em = $em;
        $this->turnover = $turnover;
    }

    protected function configure(): void
    {
        $this->setDescription('Unpublish JobPostings deleted in TurnoverIT');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $start = microtime(true);

        $io = new SymfonyStyle($input, $output);
        $io->title('App - Sync - Unpublish JobPostings deleted in TurnoverIT');

        $jobPostingsNotDeleted = $this->turnover->getJobPostings(null, null, ['id']);
        $jobPostingsNotDeletedCount = \count($jobPostingsNotDeleted);
        if (0 === $jobPostingsNotDeletedCount) {
            $io->error('Turnover API does not seem to work');

            return Command::FAILURE;
        }

        $jobPostingsToUnpublished = $this->em->getRepository(JobPosting::class)->findToUnpublish(Arrays::flatten($jobPostingsNotDeleted));
        $jobPostingsToUnpublishedCount = \count($jobPostingsToUnpublished);

        if (0 === $jobPostingsToUnpublishedCount) {
            $io->success('Nothing to unpublish');

            return Command::SUCCESS;
        }

        $io->info([
            sprintf('Date: %s', date('Y-m-d H:i:s')),
            sprintf('%d job postings to unpublish', $jobPostingsToUnpublishedCount),
        ]);

        if (false === $input->getOption('no-interaction') && false === $io->confirm('Unpublish ?')) {
            return Command::SUCCESS;
        }

        $progressBar = new ProgressBar($output, $jobPostingsToUnpublishedCount);

        foreach ($jobPostingsToUnpublished as $jobPosting) {
            /* @var JobPosting $jobPosting */
            $jobPosting->setPublished(false);

            $syncLog = (new SyncLog())
                ->setSource(SyncLogSource::CRON)
                ->setMode(SyncLogMode::UNPUBLISH)
                ->setProcessedAt(new \DateTime())
                ->setOldJobPostingId($jobPosting->getOldId())
                ->setNewJobPosting($jobPosting)
            ;
            $this->em->persist($syncLog);

            $progressBar->advance();
        }

        $this->em->flush();

        $progressBar->finish();
        $io->newLine(2);

        $io->success(sprintf('%d JonPostings unpublished', $jobPostingsToUnpublishedCount));

        $end = microtime(true);
        $duration = $end - $start;

        $io->info(sprintf('Execution time: %.2f second(s)', $duration));

        return Command::SUCCESS;
    }
}
