<?php

namespace App\Sync\Command;

use App\Core\Entity\Config;
use App\JobPosting\Entity\Application;
use App\JobPosting\Enum\ApplicationStep;
use App\Sync\Transformer\TimestampTransformer;
use App\Sync\Turnover\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ApplicationsCommand extends Command
{
    protected static $defaultName = 'app:sync:applications';
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
        $this->setDescription('Sync Applications views from TurnoverIT');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $start = microtime(true);

        $io = new SymfonyStyle($input, $output);
        $io->title('App - Sync - Applications views from TurnoverIT');

        $configName = 'sync_applications_last_datetime';

        if (null === $config = $this->em->find(Config::class, $configName)) {
            throw new \RuntimeException("Config $configName is missing.");
        }

        if (null === $config->getValue()) {
            $lastSyncDate = null;
        } elseif (false === $lastSyncDate = \DateTime::createFromFormat('Y-m-d H:i:s', $config->getValue())) {
            throw new \RuntimeException("Config $configName is invalid.");
        }

        $io->info([
            'Last sync dates',
            sprintf('%s: %s', Application::class, null === $lastSyncDate ? 'Never' : ($lastSyncDate->format('Y-m-d H:i:s') . ' (' . $lastSyncDate->getTimestamp() . ')')),
        ]);

        $applicationDatas = $this->turnover->getApplications($lastSyncDate, null);
        $applicationDatasCount = \count($applicationDatas);
        if (0 === $applicationDatasCount) {
            $io->success('No applications views to sync');

            return Command::SUCCESS;
        }

        $applicationsToUpdate = [];
        foreach ($applicationDatas as $applicationData) {
            $applications = $this->em->getRepository(Application::class)->findToUpdate($applicationData);
            foreach ($applications as $application) {
                $applicationsToUpdate[] = [
                    'application' => $application,
                    'viewedAtTimestamp' => $applicationData['viewedAtTimestamp'],
                ];
            }
        }

        $applicationsCount = \count($applicationsToUpdate);
        if (0 === $applicationsCount) {
            $io->success('Nothing to update');

            return Command::SUCCESS;
        }

        $dateSync = new \DateTime();
        $io->info([
            sprintf('Date: %s', $dateSync->format('Y-m-d H:i:s')),
            sprintf('%d applications to update', $applicationsCount),
        ]);

        if (false === $input->getOption('no-interaction') && false === $io->confirm('Update ?')) {
            return Command::SUCCESS;
        }

        $progressBar = new ProgressBar($output, $applicationsCount);

        foreach ($applicationsToUpdate as $applicationArray) {
            /** @var Application $application */
            $application = $applicationArray['application'];
            $viewedAtTimestamp = $applicationArray['viewedAtTimestamp'];

            $application
                ->setSeenAt(TimestampTransformer::transform($viewedAtTimestamp))
                ->setStep(ApplicationStep::SEEN)
            ;

            $progressBar->advance();
        }

        $applicationsSyncDateFormatted = $dateSync->format('Y-m-d H:i:s');
        $this->em->getConnection()->executeQuery("UPDATE config SET value = '$applicationsSyncDateFormatted' WHERE name = 'sync_applications_last_datetime'");
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
