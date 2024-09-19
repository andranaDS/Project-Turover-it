<?php

namespace App\Sync\Command;

use App\Company\Entity\Company;
use App\Company\Manager\CompanyManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpdateCompaniesCountsCommand extends Command
{
    protected static $defaultName = 'app:sync:update-companies-counts';

    private EntityManagerInterface $em;
    private CompanyManager $cm;

    public function __construct(EntityManagerInterface $em, CompanyManager $cm)
    {
        parent::__construct();
        $this->em = $em;
        $this->cm = $cm;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Fix JobPosting counts of Companies')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $start = microtime(true);

        $io = new SymfonyStyle($input, $output);
        $io->title('App - Core - Fix JobPosting counts of Companies');

        $companies = $this->em->getRepository(Company::class)->findAll();
        $companiesCount = \count($companies);

        if (0 === $companiesCount) {
            $io->success('Nothing to fix');

            return Command::SUCCESS;
        }

        $io->info([
            sprintf('%d companies needs to be fixed', $companiesCount),
        ]);

        if (false === $input->getOption('no-interaction') && false === $io->confirm('Synchronize ?')) {
            return Command::SUCCESS;
        }

        $progressBar = new ProgressBar($output, $companiesCount);
        foreach ($companies as $company) {
            $this->cm->updateJobPostingCounts($company);
            $progressBar->advance();
        }

        $progressBar->finish();
        $io->newLine(2);

        $this->em->flush();

        $io->success([
            sprintf('%d companies fixed', $companiesCount),
        ]);

        $end = microtime(true);
        $duration = $end - $start;

        $io->info(sprintf('Execution time: %.2f second(s)', $duration));

        return Command::SUCCESS;
    }
}
