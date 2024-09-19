<?php

namespace App\JobPosting\ElasticSearch\JobPostingsQueryBuilder;

use App\JobPosting\ElasticSearch\Filter\KeywordFilter;
use App\JobPosting\ElasticSearch\Filter\LocationFilter;
use App\JobPosting\ElasticSearch\Pagination\JobPostingsPaginator;
use App\JobPosting\Enum\PublishedSince;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\MatchPhrase;
use Elastica\Query\Nested;
use Elastica\Query\Range;
use Elastica\Query\Terms;

class JobPostingsQueryBuilder
{
    private ?Query $query = null;
    private JobPostingsPaginator $paginator;

    public function __construct(JobPostingsPaginator $paginator)
    {
        $this->paginator = $paginator;
    }

    public function createQueryBuilder(): JobPostingsQueryBuilder
    {
        $this->query = (new Query())
            ->setQuery(new BoolQuery())
            ->setTrackScores()
        ;

        return $this;
    }

    public function getQuery(): Query
    {
        if (null === $this->query) {
            throw new \LogicException('You must call the createQueryBuilder()');
        }

        return $this->query;
    }

    public function getBoolQuery(): BoolQuery
    {
        if (null === $this->query) {
            throw new \LogicException('You must call the createQueryBuilder()');
        }

        $boolQuery = $this->query->getQuery();
        if (!$boolQuery instanceof BoolQuery) {
            throw new \RuntimeException();
        }

        return $boolQuery;
    }

    public function getPaginator(int $page = null, int $itemsPerPage = null): JobPostingsPaginator
    {
        if (null === $this->query) {
            throw new \LogicException('You must call the createQueryBuilder()');
        }

        return $this->paginator->setQuery($this->query, $page, $itemsPerPage);
    }

    public function getResults(): \ArrayIterator
    {
        return $this->getPaginator()
            ->getIterator()
        ;
    }

    public function getCount(): int
    {
        return (int) $this->getPaginator()
            ->getTotalItems()
        ;
    }

    public function addKeywordFilter(array $keywords): JobPostingsQueryBuilder
    {
        if (empty($keywords)) {
            return $this;
        }

        $keywordFilter = new KeywordFilter();
        $this->getBoolQuery()->addMust($keywordFilter->build($keywords));

        return $this;
    }

    public function addRemoteFilter(array $remoteMode): JobPostingsQueryBuilder
    {
        if (empty($remoteMode)) {
            return $this;
        }
        $this->getBoolQuery()->addMust(new Terms('remoteMode', $remoteMode));

        return $this;
    }

    public function addContractFilter(array $contracts, bool $strict = false): JobPostingsQueryBuilder
    {
        if (empty($contracts)) {
            return $this;
        }

        $this->getBoolQuery()->addFilter(new Terms('contracts', $contracts));

        if (true === $strict) {
            $contractsCount = \count($contracts);
            $this->getBoolQuery()->addFilter(new Query\Script("doc['contracts'].length == $contractsCount"));
        }

        return $this;
    }

    public function addLocationKeysFilter(array $locations): JobPostingsQueryBuilder
    {
        if (empty($locations)) {
            return $this;
        }

        $locationFilter = new LocationFilter();
        $this->getBoolQuery()->addMust($locationFilter->build($locations));

        return $this;
    }

    public function addPublishedFilter(): JobPostingsQueryBuilder
    {
        $this->getBoolQuery()->addFilter(new Range('publishedAt', ['lte' => 'now']));
        $this->getBoolQuery()->addFilter(new MatchPhrase('published', true));

        return $this;
    }

    public function addStatusFilter(string $status): JobPostingsQueryBuilder
    {
        $this->getBoolQuery()->addFilter(new MatchPhrase('status', $status));

        return $this;
    }

    public function addSkillsFilter(array $skills): JobPostingsQueryBuilder
    {
        if (empty($skills)) {
            return $this;
        }

        $nestedQuery = new Nested();
        $nestedQuery->setPath('skills');
        $nestedQuery->setQuery(new Terms('skills.slug', $skills));
        $this->getBoolQuery()->addFilter($nestedQuery);

        return $this;
    }

    public function addMinDurationFilter(?int $minDuration): JobPostingsQueryBuilder
    {
        if (null === $minDuration) {
            return $this;
        }

        $this->getBoolQuery()->addMust(new Range('duration', ['gte' => $minDuration]));

        return $this;
    }

    public function addMaxDurationFilter(?int $maxDuration): JobPostingsQueryBuilder
    {
        if (null === $maxDuration) {
            return $this;
        }

        $this->getBoolQuery()->addMust(new Range('duration', ['lte' => $maxDuration]));

        return $this;
    }

    public function addMinSalaryFilter(?int $minAnnualSalary, ?int $minDailySalary): JobPostingsQueryBuilder
    {
        if (null === $minAnnualSalary && null === $minDailySalary) {
            return $this;
        }

        if (null !== $minAnnualSalary && null === $minDailySalary) {
            $this->getBoolQuery()->addMust(new Range('maxAnnualSalary', ['gte' => $minAnnualSalary]));
        }

        if (null === $minAnnualSalary && null !== $minDailySalary) {
            $this->getBoolQuery()->addMust(new Range('maxDailySalary', ['gte' => $minDailySalary]));
        }

        $boolQuery = new BoolQuery();
        $boolQuery->addShould(new Range('maxAnnualSalary', ['gte' => $minAnnualSalary]));
        $boolQuery->addShould(new Range('maxDailySalary', ['gte' => $minDailySalary]));
        $boolQuery->setMinimumShouldMatch(1);
        $this->getBoolQuery()->addMust($boolQuery);

        return $this;
    }

    public function addDailySalaryFilter(?int $minDailySalary = null, ?int $maxDailySalary = null): JobPostingsQueryBuilder
    {
        if (null === $minDailySalary && null === $maxDailySalary) {
            return $this;
        }

        if (null !== $minDailySalary) {
            $this->getBoolQuery()->addFilter(new Range('maxDailySalary', ['gte' => $minDailySalary]));
        }

        if (null !== $maxDailySalary) {
            $this->getBoolQuery()->addFilter(new Range('minDailySalary', ['lte' => $maxDailySalary]));
        }

        return $this;
    }

    public function addPublishedSinceFilter(?string $publishedSince, string $timezone = 'Europe/Paris'): JobPostingsQueryBuilder
    {
        if (null === $publishedSince) {
            return $this;
        }

        $range = [];

        switch ($publishedSince) {
            case PublishedSince::LESS_THAN_24_HOURS:
                $range = [
                    'gte' => sprintf('now-%sd/d', 1),
                    'time_zone' => $timezone,
                ];
                break;
            case PublishedSince::FROM_1_TO_7_DAYS:
                $range = [
                    'lt' => sprintf('now-%sd/d', 1),
                    'gte' => sprintf('now-%sd/d', 7),
                    'time_zone' => $timezone,
                ];
                break;
            case PublishedSince::FROM_8_TO_14_DAYS:
                $range = [
                    'lt' => sprintf('now-%sd/d', 7),
                    'gte' => sprintf('now-%sd/d', 14),
                    'time_zone' => $timezone,
                ];
                break;
            case PublishedSince::FROM_15_DAYS_TO_1_MONTH:
                $range = [
                    'lt' => sprintf('now-%sd/d', 14),
                    'gte' => sprintf('now-%sd/d', 30),
                    'time_zone' => $timezone,
                ];
                break;
        }

        $this->getBoolQuery()->addFilter(new Range('publishedAt', $range));

        return $this;
    }

    public function addPublishedAfterFilter(\DateTime $after = null, string $timezone = 'Europe/Paris'): JobPostingsQueryBuilder
    {
        if (null === $after) {
            return $this;
        }

        $this->getBoolQuery()->addFilter(new Range('publishedAt', [
            'gte' => $after->format(\DateTimeInterface::RFC3339),
            'time_zone' => $timezone,
        ]));

        return $this;
    }

    public function addPublishedBeforeFilter(\DateTime $before = null, string $timezone = 'Europe/Paris'): JobPostingsQueryBuilder
    {
        if (null === $before) {
            return $this;
        }

        $this->getBoolQuery()->addFilter(new Range('publishedAt', [
            'lte' => $before->format(\DateTimeInterface::RFC3339),
            'time_zone' => $timezone,
        ]));

        return $this;
    }

    public function addStartsAfterFilter(\DateTime $after = null, string $timezone = 'Europe/Paris'): JobPostingsQueryBuilder
    {
        if (null === $after) {
            return $this;
        }

        $this->getBoolQuery()->addFilter(new Range('startsAt', [
            'gte' => $after->format('Y-m-d'),
            'time_zone' => $timezone,
        ]));

        return $this;
    }

    public function addStartsBeforeFilter(\DateTime $before = null, string $timezone = 'Europe/Paris'): JobPostingsQueryBuilder
    {
        if (null === $before) {
            return $this;
        }

        $this->getBoolQuery()->addFilter(new Range('startsAt', [
            'lte' => $before->format('Y-m-d'),
            'time_zone' => $timezone,
        ]));

        return $this;
    }

    public function addKeywordOrSkillsFilter(array $keywords, array $skills): JobPostingsQueryBuilder
    {
        if (empty($keywords) || empty($skills)) {
            return $this;
        }

        $boolQuery = new BoolQuery();

        $keywordFilter = new KeywordFilter();
        $boolQuery->addShould($keywordFilter->build($keywords));

        $nestedQuery = new Nested();
        $nestedQuery->setPath('skills');
        $nestedQuery->setQuery(new Terms('skills.slug', $skills));
        $boolQuery->addShould($nestedQuery);

        $boolQuery->setMinimumShouldMatch(1);

        $this->getBoolQuery()->addMust($boolQuery);

        return $this;
    }

    public function addJobsFilter(array $jobs): JobPostingsQueryBuilder
    {
        if (empty($jobs)) {
            return $this;
        }

        $nestedQuery = new Nested();
        $nestedQuery->setPath('job');
        $nestedQuery->setQuery(new Terms('job.nameForContributionSlug', $jobs));
        $this->getBoolQuery()->addFilter($nestedQuery);

        return $this;
    }

    public function addCompanyBusinessActivityFilter(?string $companyBusinessActivity): JobPostingsQueryBuilder
    {
        if (null === $companyBusinessActivity) {
            return $this;
        }

        $companyNestedQuery = new Nested();
        $companyNestedQuery->setPath('company');

        $businessNestedQuery = new Nested();
        $businessNestedQuery->setPath('company.businessActivity');

        $nameCompanyBusinessActivityQuery = new MatchPhrase('company.businessActivity.slug', $companyBusinessActivity);

        $businessNestedQuery->setQuery($nameCompanyBusinessActivityQuery);
        $companyNestedQuery->setQuery($businessNestedQuery);

        $this->getBoolQuery()->addFilter($companyNestedQuery);

        return $this;
    }

    public function addStartsAt(\DateTime $startsAt = null, string $timezone = 'Europe/Paris'): JobPostingsQueryBuilder
    {
        if (null === $startsAt) {
            return $this;
        }

        $this->getBoolQuery()->addFilter(new Range('startsAt', [
            'gte' => $startsAt->format('Y-m-d'),
            'time_zone' => $timezone,
        ]));

        return $this;
    }
}
