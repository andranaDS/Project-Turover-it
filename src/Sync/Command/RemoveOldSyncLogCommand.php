<?php

namespace App\Sync\Command;

use App\Sync\Entity\SyncLog;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RemoveOldSyncLogCommand extends Command
{
    protected static $defaultName = 'app:sync:remove-old-log';
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Remove old sync logs')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $start = microtime(true);

        $io = new SymfonyStyle($input, $output);
        $io->title('App - Sync - Remove old sync logs');

        $thirtyDaysAgo = Carbon::now()->subDays(30);
        $countLogs = $this->em->getRepository(SyncLog::class)->countOldLogs($thirtyDaysAgo);

        if (0 === $countLogs) {
            $io->success('Nothing to update');

            return Command::SUCCESS;
        }

        $io->info([
            sprintf('Date: %s', date('Y-m-d H:i:s')),
            sprintf('%d sync logs to delete', $countLogs),
        ]);

        if (false === $input->getOption('no-interaction') && false === $io->confirm('Delete ?')) {
            return Command::SUCCESS;
        }

        $this->em->getRepository(SyncLog::class)->deleteOldLogs($thirtyDaysAgo);
        $this->em->flush();

        $io->success("$countLogs sync logs deleted");

        $end = microtime(true);
        $duration = $end - $start;

        $io->info(sprintf('Execution time: %.2f second(s)', $duration));

        return Command::SUCCESS;
    }
}
