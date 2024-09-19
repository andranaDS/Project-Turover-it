<?php

namespace App\JobPosting\Command;

use App\JobPosting\Entity\JobPosting;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class JobPostingDeleteOldDraftsCommand extends Command
{
    protected static $defaultName = 'app:job-posting:delete-old-drafts';
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure(): void
    {
        $this->setDescription('App - JobPosting - Delete old drafts (30 days)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('App - JobPosting - Delete old drafts (30 days)');

        $jobPostings = $this->em->getRepository(JobPosting::class)->findDepractedDraftToDelete();
        $dataCount = \count($jobPostings);

        $jobPostingDeletedCount = 0;

        $io->progressStart($dataCount);

        foreach ($jobPostings as $jobPosting) {
            /* @var JobPosting $jobPosting */
            $this->em->remove($jobPosting);
            ++$jobPostingDeletedCount;
            $io->progressAdvance();
        }

        $this->em->flush();
        $io->progressFinish();

        $io->success("$jobPostingDeletedCount JobPostings softDeleted");

        return Command::SUCCESS;
    }
}
