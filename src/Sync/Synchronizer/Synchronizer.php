<?php

namespace App\Sync\Synchronizer;

use App\Company\Entity\Company;
use App\JobPosting\Entity\JobPosting;
use App\Sync\Enum\SyncLogSource;

class Synchronizer
{
    private array $synchronizers;

    public function __construct(CompanySynchronizer $companySynchronizer, JobPostingSynchronizer $jobPostingSynchronizer)
    {
        $this->synchronizers = [
            Company::class => $companySynchronizer,
            JobPosting::class => $jobPostingSynchronizer,
        ];
    }

    private function getSynchronizer(string $entity): SynchronizerInterface
    {
        if (false === isset($this->synchronizers[$entity])) {
            throw new \InvalidArgumentException(sprintf('Synchronizer for class "%s" not found', $entity));
        }

        return $this->synchronizers[$entity];
    }

    public function synchronize(string $entity, array $data, string $source = SyncLogSource::CRON): array
    {
        $synchronizer = $this->getSynchronizer($entity);

        return $synchronizer->synchronize($data, $source);
    }
}
