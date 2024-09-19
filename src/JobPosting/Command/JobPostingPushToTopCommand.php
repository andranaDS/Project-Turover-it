<?php

namespace App\JobPosting\Command;

use App\JobPosting\Entity\JobPosting;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class JobPostingPushToTopCommand extends Command
{
    protected static $defaultName = 'app:job-posting:push-to-top';
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure(): void
    {
        $this->setDescription('App - JobPosting - Push to top');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('App - JobPosting - Push to top');

        $jobPostings = $this->em->getRepository(JobPosting::class)->findToPushToTop();
        $dataCount = \count($jobPostings);

        $jobPostingPushedCount = 0;

        $io->progressStart($dataCount);

        foreach ($jobPostings as $jobPosting) {
            /* @var JobPosting $jobPosting */
            $jobPosting
                ->setPushToTop(true)
                ->setPushedToTopCount((int) $jobPosting->getPushedToTopCount() + 1)
                ->setPushedToTopAt(Carbon::now())
            ;
            ++$jobPostingPushedCount;
            $io->progressAdvance();
        }

        $this->em->flush();
        $io->progressFinish();

        $io->success("$jobPostingPushedCount JobPostings pushed to top");

        return Command::SUCCESS;
    }
}
