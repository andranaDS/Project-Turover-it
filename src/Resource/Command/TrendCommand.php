<?php

namespace App\Resource\Command;

use App\Resource\Manager\TrendManager;
use Carbon\Carbon;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TrendCommand extends Command
{
    protected static $defaultName = 'app:resource:trend';
    private TrendManager $tm;

    public function __construct(TrendManager $tm)
    {
        parent::__construct();
        $this->tm = $tm;
    }

    protected function configure(): void
    {
        $this->setDescription('Create trend of the week');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $start = microtime(true);

        $io = new SymfonyStyle($input, $output);
        $io->title('App - Resource - Create trend of a week');

        $now = Carbon::now();
        $week = $now->startOfWeek();

        if (true === $this->tm->trendExists($week)) {
            $io->success(sprintf('Trend "%s" already exists. Nothing to create.', $week->format(\DateTimeInterface::RFC3339)));

            return Command::SUCCESS;
        }

        $this->tm->createTrend($week);

        $io->success(sprintf('Trend "%s" created.', $week->format(\DateTimeInterface::RFC3339)));

        $end = microtime(true);
        $duration = $end - $start;

        $io->info(sprintf('Execution time: %.2f second(s)', $duration));

        return Command::SUCCESS;
    }
}
