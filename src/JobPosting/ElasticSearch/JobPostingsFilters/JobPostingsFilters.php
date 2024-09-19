<?php

namespace App\JobPosting\ElasticSearch\JobPostingsFilters;

class JobPostingsFilters
{
    public const ORDER_RELEVANCE = 'relevance';
    public const ORDER_DATE = 'date';
    public const ORDER_MIN_DAILY_SALARY = 'salary';

    protected ?int $minDuration = null;
    protected ?int $maxDuration = null;
    protected array $contracts = [];
    protected ?string $publishedSince = null;
    protected array $locationKeys = [];
    protected ?int $minAnnualSalary = null;
    protected ?int $minDailySalary = null;
    protected array $remoteMode = [];
    protected array $skills = [];
    protected array $keywords = [];
    protected array $jobs = [];
    protected ?\DateTime $publishedAfter = null;
    protected ?\DateTime $publishedBefore = null;
    protected ?\DateTime $startsAfter = null;
    protected ?\DateTime $startsBefore = null;
    protected string $order = self::ORDER_RELEVANCE;
    protected ?\DateTime $startsAt = null;
    protected ?string $companyBusinessActivity = null;
    protected ?bool $intercontractOnly = false;

    public function getIntercontractOnly(): ?bool
    {
        return $this->intercontractOnly;
    }

    public function setIntercontractOnly(?bool $strict): self
    {
        $this->intercontractOnly = $strict;

        return $this;
    }

    public function getMinDuration(): ?int
    {
        return $this->minDuration;
    }

    public function setMinDuration(?int $minDuration): self
    {
        $this->minDuration = $minDuration;

        return $this;
    }

    public function getMaxDuration(): ?int
    {
        return $this->maxDuration;
    }

    public function setMaxDuration(?int $maxDuration): self
    {
        $this->maxDuration = $maxDuration;

        return $this;
    }

    public function getContracts(): array
    {
        return $this->contracts;
    }

    public function setContracts(array $contracts): self
    {
        $this->contracts = $contracts;

        return $this;
    }

    public function getPublishedSince(): ?string
    {
        return $this->publishedSince;
    }

    public function setPublishedSince(?string $publishedSince): self
    {
        $this->publishedSince = $publishedSince;

        return $this;
    }

    public function getLocationKeys(): array
    {
        return $this->locationKeys;
    }

    public function setLocationKeys(array $locationKeys): self
    {
        $this->locationKeys = $locationKeys;

        return $this;
    }

    public function getMinAnnualSalary(): ?int
    {
        return $this->minAnnualSalary;
    }

    public function setMinAnnualSalary(?int $minAnnualSalary): self
    {
        $this->minAnnualSalary = $minAnnualSalary;

        return $this;
    }

    public function getMinDailySalary(): ?int
    {
        return $this->minDailySalary;
    }

    public function setMinDailySalary(?int $minDailySalary): self
    {
        $this->minDailySalary = $minDailySalary;

        return $this;
    }

    public function getRemoteMode(): array
    {
        return $this->remoteMode;
    }

    public function setRemoteMode(array $remoteMode): self
    {
        $this->remoteMode = $remoteMode;

        return $this;
    }

    public function getSkills(): array
    {
        return $this->skills;
    }

    public function setSkills(array $skills): self
    {
        $this->skills = $skills;

        return $this;
    }

    public function getKeywords(): array
    {
        return $this->keywords;
    }

    public function setKeywords(array $keywords): self
    {
        $this->keywords = $keywords;

        return $this;
    }

    public function getPublishedAfter(): ?\DateTime
    {
        return $this->publishedAfter;
    }

    public function setPublishedAfter(?\DateTime $publishedAfter): JobPostingsFilters
    {
        $this->publishedAfter = $publishedAfter;

        return $this;
    }

    public function getPublishedBefore(): ?\DateTime
    {
        return $this->publishedBefore;
    }

    public function setPublishedBefore(?\DateTime $publishedBefore): JobPostingsFilters
    {
        $this->publishedBefore = $publishedBefore;

        return $this;
    }

    public function getStartsAfter(): ?\DateTime
    {
        return $this->startsAfter;
    }

    public function setStartsAfter(?\DateTime $startsAfter): JobPostingsFilters
    {
        $this->startsAfter = $startsAfter;

        return $this;
    }

    public function getStartsBefore(): ?\DateTime
    {
        return $this->startsBefore;
    }

    public function setStartsBefore(?\DateTime $startsBefore): JobPostingsFilters
    {
        $this->startsBefore = $startsBefore;

        return $this;
    }

    public function getJobs(): array
    {
        return $this->jobs;
    }

    public function setJobs(array $jobs): JobPostingsFilters
    {
        $this->jobs = $jobs;

        return $this;
    }

    public static function buildArray(?string $value): array
    {
        return empty($value) ? [] : array_values(array_filter(explode(',', $value)));
    }

    public static function buildString(?string $value): ?string
    {
        return empty($value) ? null : $value;
    }

    public static function buildInteger(?string $value): ?int
    {
        return null === $value ? null : (int) $value;
    }

    public static function buildDatetime(?string $value): ?\DateTime
    {
        return null === $value ? null : new \DateTime($value);
    }

    public static function buildBoolean(?string $value): bool
    {
        if ('true' === $value) {
            return true;
        }

        return false;
    }

    public function getOrder(): string
    {
        return $this->order;
    }

    public function setOrder(string $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function getStartsAt(): ?\DateTime
    {
        return $this->startsAt;
    }

    public function setStartsAt(?\DateTime $startsAt): JobPostingsFilters
    {
        $this->startsAt = $startsAt;

        return $this;
    }

    public function getCompanyBusinessActivity(): ?string
    {
        return $this->companyBusinessActivity;
    }

    public function setCompanyBusinessActivity(?string $companyBusinessActivity): JobPostingsFilters
    {
        $this->companyBusinessActivity = $companyBusinessActivity;

        return $this;
    }
}
