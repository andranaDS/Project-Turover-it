<?php

namespace App\Resource\Command;

use App\Resource\Manager\JobContributionsStatisticsManager;
use App\Resource\Repository\JobContributionStatisticsRepository;
use Carbon\Carbon;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class JobContributionStatisticsCommand extends Command
{
    protected static $defaultName = 'app:resource:job-contribution-statistics';
    private JobContributionsStatisticsManager $jobContributionsStatisticsManager;
    private JobContributionStatisticsRepository $jobContributionStatisticsRepository;

    public function __construct(
        JobContributionsStatisticsManager $jobContributionsStatisticsManager,
        JobContributionStatisticsRepository $jobContributionStatisticsRepository
    ) {
        parent::__construct();
        $this->jobContributionsStatisticsManager = $jobContributionsStatisticsManager;
        $this->jobContributionStatisticsRepository = $jobContributionStatisticsRepository;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Create job contributions statistics of a day')
            ->addOption('limit', null, InputOption::VALUE_OPTIONAL)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $time = -microtime(true);

        $io = new SymfonyStyle($input, $output);
        $io->title('App - Resource - Create job contributions statistics of a day');

        $day = Carbon::now()->startOfDay();

        if (false === empty($this->jobContributionStatisticsRepository->findBy(['day' => $day], [], 1))) {
            $io->success(sprintf('Job contributions statistics "%s" already exists. Nothing to create.', $day->format(\DateTimeInterface::RFC3339)));

            return Command::SUCCESS;
        }

        $this->jobContributionsStatisticsManager->createStatistics($day, $input->getOption('limit'));
        $io->success(sprintf('job contributions statistics "%s" created.', $day->format(\DateTimeInterface::RFC3339)));

        $time += microtime(true);

        $io->info(sprintf('Execution time: %.2f second(s)', $time));

        return Command::SUCCESS;
    }
}
