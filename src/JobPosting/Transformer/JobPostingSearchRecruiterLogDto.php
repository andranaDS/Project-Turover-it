<?php

namespace App\JobPosting\Transformer;

use App\Company\Entity\CompanyBusinessActivity;
use App\Core\Entity\LocationKeyLabel;
use App\JobPosting\Traits\JobPostingRecruiterSearchFiltersTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

class JobPostingSearchRecruiterLogDto
{
    use JobPostingRecruiterSearchFiltersTrait;

    private ?Collection $locations = null;

    public function __construct(EntityManagerInterface $em, array $queryParams)
    {
        $this->setKeywords($queryParams['keywords'] ?? null);
        $this->setIntercontractOnly($queryParams['intercontractOnly'] ?? false);
        $this->setMinDuration($queryParams['minDuration'] ?? null);
        $this->setMaxDuration($queryParams['maxDuration'] ?? null);
        $this->setMinDailySalary($queryParams['minDailySalary'] ?? null);
        $this->setMaxDailySalary($queryParams['maxDailySalary'] ?? null);
        $this->setStartsAt($queryParams['startsAt'] ?? null);
        $this->setRemoteMode($queryParams['remoteMode'] ?? null);
        $this->setPublishedSince($queryParams['publishedSince'] ?? null);

        if (\array_key_exists('businessActivity', $queryParams)) {
            $this->setBusinessActivity($em->getRepository(CompanyBusinessActivity::class)->findOneBySlug($queryParams['businessActivity']));
        }

        if (\array_key_exists('locations', $queryParams)) {
            $locationsCollection = new ArrayCollection();
            $locations = explode(',', $queryParams['locations']);
            foreach ($locations as $locationKey) {
                $location = $em->getRepository(LocationKeyLabel::class)->findOneByKey($locationKey);
                if ($location) {
                    $locationsCollection->add($location->getData());
                }
            }
            $this->setLocations($locationsCollection);
        }
    }

    public function setIntercontractOnly(?string $intercontractOnly): void
    {
        $this->intercontractOnly = 'true' === $intercontractOnly;
    }

    public function setMinDuration(?string $minDuration): void
    {
        $this->minDuration = (int) $minDuration;
    }

    public function setMaxDuration(?string $maxDuration): void
    {
        $this->maxDuration = (int) $maxDuration;
    }

    public function setMinDailySalary(?string $minDailySalary): void
    {
        $this->minDailySalary = (int) $minDailySalary;
    }

    public function setMaxDailySalary(?string $maxDailySalary): void
    {
        $this->maxDailySalary = (int) $maxDailySalary;
    }

    public function setStartsAt(?string $startsAt): void
    {
        $this->startsAt = $startsAt ? new \DateTime($startsAt) : null;
    }

    public function setBusinessActivity(?CompanyBusinessActivity $companyBusinessActivity): void
    {
        $this->businessActivity = $companyBusinessActivity;
    }

    public function setRemoteMode(?string $remoteMode): void
    {
        $this->remoteMode = $remoteMode ? explode(',', $remoteMode) : null;
    }

    public function setLocations(Collection $locations): void
    {
        $this->locations = $locations;
    }

    public function getLocations(): ?Collection
    {
        return $this->locations;
    }
}
