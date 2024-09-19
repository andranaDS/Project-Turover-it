<?php

namespace App\Sync\Command;

use App\Company\Entity\Company;
use App\Core\Entity\Config;
use App\Core\Util\Arrays;
use App\JobPosting\Entity\JobPosting;
use App\Sync\Synchronizer\Synchronizer;
use App\Sync\Turnover\Client;
use App\Sync\Util\Converter;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Utils\Json;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Webmozarts\Console\Parallelization\ContainerAwareCommand;
use Webmozarts\Console\Parallelization\Parallelization;
use Webmozarts\Console\Parallelization\ParallelizationInput;

class ExecuteNotPublishedCommand extends ContainerAwareCommand
{
    use Parallelization;

    protected static $defaultName = 'app:sync:execute-not-published';
    private EntityManagerInterface $em;
    private Client $turnover;
    private Synchronizer $synchronizer;
    private array $syncDates = [];
    private SymfonyStyle $io;

    public function __construct(EntityManagerInterface $em, Client $turnover, Synchronizer $synchronizer)
    {
        parent::__construct();
        $this->em = $em;
        $this->turnover = $turnover;
        $this->synchronizer = $synchronizer;
    }

    protected function configure(): void
    {
        self::configureParallelization($this);
    }

    protected function getItemName(int $count): string
    {
        return 1 === $count ? 'object' : 'objects';
    }

    protected function getSegmentSize(): int
    {
        return 100;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->initSyncDates();

        $this->io = new SymfonyStyle($input, $output);
        $parallelizationInput = new ParallelizationInput($input);

        $start = microtime(true);

        if ($parallelizationInput->isChildProcess()) {
            $this->executeChildProcess($input, $output);

            return Command::SUCCESS;
        }

        $this->executeMasterProcess($parallelizationInput, $input, $output);

        $end = microtime(true);
        $duration = $end - $start;
        $this->io->info(sprintf('Execution time: %.2f second(s)', $duration));

        return Command::SUCCESS;
    }

    protected function fetchItems(InputInterface $input): array
    {
        $companiesSyncDate = $this->getSyncDate(Company::class);
        $jobPostingsSyncDate = $this->getSyncDate(JobPosting::class);

        $this->io->info([
            'Last sync dates',
            sprintf('%s: %s', Company::class, null === $companiesSyncDate ? 'Never' : ($companiesSyncDate->format('Y-m-d H:i:s') . ' (' . $companiesSyncDate->getTimestamp() . ')')),
            sprintf('%s: %s', JobPosting::class, null === $jobPostingsSyncDate ? 'Never' : ($jobPostingsSyncDate->format('Y-m-d H:i:s') . ' (' . $jobPostingsSyncDate->getTimestamp() . ')')),
        ]);

        $entitiesToSyncCount = 5000;
        $companies = $this->getCompaniesToSync($companiesSyncDate, $entitiesToSyncCount);
        $companiesCount = \count($companies);

        $entitiesToSyncCount = max(0, $entitiesToSyncCount - $companiesCount);
        $jobPostings = $entitiesToSyncCount > 0 ? $this->getJobPostingsToSync($jobPostingsSyncDate, $entitiesToSyncCount, false) : [];

        $jobPostingsCount = \count($jobPostings);

        $this->io->info([
            'Entities to sync',
            sprintf('%s to sync: %d', Company::class, $companiesCount),
            sprintf('%s to sync: %d', JobPosting::class, $jobPostingsCount),
        ]);

        $objects = array_merge($companies, $jobPostings);
        $objectsCount = $companiesCount + $jobPostingsCount;

        if (0 === $objectsCount) {
            $this->io->success('Nothing to sync');

            return [];
        }

        return $objects;
    }

    protected function runSingleCommand(string $item, InputInterface $input, OutputInterface $output): void
    {
        $item = Json::decode($item, Json::FORCE_ARRAY);
        $object = $item['object'];

        $data = Converter::convertArray($item['data']);

        $result = $this->synchronizer->synchronize($object, $data);

        $this->syncDates[$object] = $result['requestedAt'];
    }

    protected function runAfterBatch(InputInterface $input, OutputInterface $output, array $items): void
    {
        $this->em->flush();
        $this->updateSyncDates();
        gc_collect_cycles();
    }

    private function getCompaniesToSync(?\DateTime $date = null, ?int $limit = null): array
    {
        if (0 === $limit) {
            return [];
        }

        return Arrays::map($this->turnover->getCompanies($date, $limit), static function (array $data) {
            return Json::encode([
                'object' => Company::class,
                'data' => $data,
            ]);
        });
    }

    private function getJobPostingsToSync(?\DateTime $date = null, ?int $limit = null, ?bool $published = null): array
    {
        if (0 === $limit) {
            return [];
        }

        return Arrays::map($this->turnover->getJobPostings($date, $limit, [], $published), static function (array $data) {
            return Json::encode([
                'object' => JobPosting::class,
                'data' => $data,
            ]);
        });
    }

    private function getSyncDate(string $objectClass): ?\DateTime
    {
        if (false === \array_key_exists($objectClass, $this->syncDates)) {
            throw new \RuntimeException("Sync date for '$objectClass' is missing.");
        }

        return $this->syncDates[$objectClass];
    }

    private function initSyncDates(): void
    {
        $this->initObjectSyncDate(Company::class, 'sync_execute_companies_last_datetime');
        $this->initObjectSyncDate(JobPosting::class, 'sync_execute_job_postings_last_datetime');
    }

    private function initObjectSyncDate(string $objectClass, string $configName): void
    {
        if (null === $config = $this->em->find(Config::class, $configName)) {
            throw new \RuntimeException("Config '$configName' is missing.");
        }

        if (null === $config->getValue()) {
            $date = null;
        } elseif (false === $date = \DateTime::createFromFormat('Y-m-d H:i:s', $config->getValue())) {
            throw new \RuntimeException("Config '$configName' is invalid.");
        }

        $this->syncDates[$objectClass] = $date;
    }

    public function updateSyncDates(): void
    {
        $companiesSyncDate = $this->syncDates[Company::class] ?? null;
        if ($companiesSyncDate instanceof \DateTime) {
            $companiesSyncDateFormatted = $companiesSyncDate->format('Y-m-d H:i:s');
            $this->em->getConnection()->executeQuery("UPDATE config SET value = '$companiesSyncDateFormatted' WHERE name = 'sync_execute_companies_last_datetime'");
        }

        $jobPostingsSyncDate = $this->syncDates[JobPosting::class] ?? null;
        if ($jobPostingsSyncDate instanceof \DateTime) {
            $jobPostingsSyncDateFormatted = $jobPostingsSyncDate->format('Y-m-d H:i:s');
            $this->em->getConnection()->executeQuery("UPDATE config SET value = '$jobPostingsSyncDateFormatted' WHERE name = 'sync_execute_job_postings_last_datetime'");
        }
    }
}
