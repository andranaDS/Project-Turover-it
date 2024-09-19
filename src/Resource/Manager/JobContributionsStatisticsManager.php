<?php

namespace App\Resource\Manager;

use App\Core\Entity\Job;
use App\Core\Util\Numbers;
use App\Resource\Entity\JobContributionStatistics;
use App\Resource\Enum\Employer;
use App\Resource\Enum\FoundBy;
use App\Resource\Enum\Location;
use App\Resource\Repository\ContributionRepository;
use App\User\Enum\ExperienceYear;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use MathPHP\Statistics\Average;
use MathPHP\Statistics\Descriptive;

class JobContributionsStatisticsManager
{
    private EntityManagerInterface $em;
    private ContributionRepository $contributionRepository;

    public function __construct(EntityManagerInterface $em, ContributionRepository $contributionRepository)
    {
        $this->em = $em;
        $this->contributionRepository = $contributionRepository;
    }

    public function createStatistics(Carbon $day, ?int $limit = null): void
    {
        $jobs = $this->em->getRepository(Job::class)->findAllAsIterable();
        $end = $day->copy()->subDays(30);

        foreach ($jobs as $job) {
            $jobContributionsStatistics = $this->createStatisticsByJob($day, $end, $job, $limit);
            $this->em->persist($jobContributionsStatistics);
        }

        $this->em->flush();
    }

    private function createStatisticsByJob(Carbon $day, Carbon $end, Job $job, ?int $limit): JobContributionStatistics
    {
        return (new JobContributionStatistics())
            ->setJob($job)
            ->setDay($day)
            ->setRemoteDaysPerWeekDistributionWork($this->createRemoteDaysPerWeekDistribution($job, $day, $end, $limit, false))
            ->setRemoteDaysPerWeekDistributionFree($this->createRemoteDaysPerWeekDistribution($job, $day, $end, $limit, true))
            ->setExperienceYearDistributionWork($this->createExperienceYearDistribution($job, $day, $end, $limit, false))
            ->setExperienceYearDistributionFree($this->createExperienceYearDistribution($job, $day, $end, $limit, true))
            ->setEmployerDistributionWork($this->createEmployerDistributionWork($job, $day, $end, $limit))
            ->setFoundByDistributionFree($this->createFoundByDistributionFree($job, $day, $end, $limit))
            ->setContractDurationDistributionFree($this->createContractDurationDistributionFree($job, $day, $end, $limit))
            ->setContractDistributionWork($this->createContractDistributionWork($job, $day, $end, $limit))
            ->setOnCallPercentageFree($this->createOnCallPercentage($job, $day, $end, $limit, true))
            ->setOnCallPercentageWork($this->createOnCallPercentage($job, $day, $end, $limit, false))
            ->setAverageSearchJobDurationWork($this->createAverageSearchJobDuration($job, $day, $end, $limit, false))
            ->setAverageSearchJobDurationFree($this->createAverageSearchJobDuration($job, $day, $end, $limit, true))
            ->setAverageDailySalaryDirectly($this->createAverageSalaryByFoundBy($job, $day, $end, FoundBy::DIRECTLY, $limit, true))
            ->setAverageDailySalaryWithIntermediary($this->createAverageSalaryByFoundBy($job, $day, $end, FoundBy::INTERMEDIARY, $limit, true))
            ->setAverageAnnualSalaryNonFinalClient($this->createAverageSalaryByEmployer($job, $day, $end, Employer::getNonFinalClientValues(), $limit, false))
            ->setAverageAnnualSalaryFinalClient($this->createAverageSalaryByEmployer($job, $day, $end, Employer::getFinalClientValues(), $limit, false))
            ->setSalaryExperienceDistributionFree($this->createSalaryExperienceDistribution($job, $day, $end, $limit, true))
            ->setSalaryExperienceDistributionWork($this->createSalaryExperienceDistribution($job, $day, $end, $limit, false))
            ->setSalaryExperienceLocationDistributionFree($this->createSalaryExperienceLocationDistribution($job, $day, $end, $limit, true))
            ->setSalaryExperienceLocationDistributionWork($this->createSalaryExperienceLocationDistribution($job, $day, $end, $limit, false))
        ;
    }

    public static function formatDistribution(array $data): array
    {
        $total = array_sum(array_map(static function (array $d) {
            return $d['count'];
        }, $data));

        $distribution = [];
        foreach ($data as $d) {
            $distribution[] = [
                'value' => $d['value'],
                'count' => $d['count'],
                'percentage' => round($d['count'] / $total, 2),
            ];
        }

        return $distribution;
    }

    private function createRemoteDaysPerWeekDistribution(Job $job, \DateTimeInterface $start, \DateTimeInterface $end, ?int $limit, bool $isFree): array
    {
        $counts = $this->contributionRepository->countRemoteDaysPerWeek(
            $job,
            $start,
            $end,
            $limit,
            $isFree
        );

        return self::formatDistribution($counts);
    }

    private function createExperienceYearDistribution(Job $job, \DateTimeInterface $start, \DateTimeInterface $end, ?int $limit, bool $isFree): array
    {
        $counts = $this->contributionRepository->countExperienceYear(
            $job,
            $start,
            $end,
            $limit,
            $isFree
        );

        return self::formatDistribution($counts);
    }

    private function createEmployerDistributionWork(Job $job, \DateTimeInterface $start, \DateTimeInterface $end, ?int $limit): array
    {
        $counts = $this->contributionRepository->countEmployer(
            $job,
            $start,
            $end,
            $limit,
            false
        );

        return self::formatDistribution($counts);
    }

    private function createFoundByDistributionFree(Job $job, \DateTimeInterface $start, \DateTimeInterface $end, ?int $limit): array
    {
        $counts = $this->contributionRepository->countFoundBy(
            $job,
            $start,
            $end,
            $limit,
            true
        );

        return self::formatDistribution($counts);
    }

    private function createContractDurationDistributionFree(Job $job, \DateTimeInterface $start, \DateTimeInterface $end, ?int $limit): array
    {
        $counts = $this->contributionRepository->countContractDuration(
            $job,
            $start,
            $end,
            $limit,
            true
        );

        return self::formatDistribution($counts);
    }

    private function createContractDistributionWork(Job $job, \DateTimeInterface $start, \DateTimeInterface $end, ?int $limit): array
    {
        $counts = $this->contributionRepository->countContract(
            $job,
            $start,
            $end,
            $limit,
            false
        );

        return self::formatDistribution($counts);
    }

    private function createOnCallPercentage(Job $job, \DateTimeInterface $start, \DateTimeInterface $end, ?int $limit, bool $isFree): ?int
    {
        $data = $this->contributionRepository->countOnCall(
            $job,
            $start,
            $end,
            $limit,
            $isFree
        );

        $total = array_sum(array_map(static function (array $d) {
            return $d['count'];
        }, $data));

        foreach ($data as $d) {
            if (true === $d['value']) {
                return (int) ($d['count'] / $total * 100);
            }
        }

        return null;
    }

    private function createSalaryExperienceDistribution(Job $job, \DateTimeInterface $start, \DateTimeInterface $end, ?int $limit, bool $isFree): ?array
    {
        $data = [];

        foreach (ExperienceYear::getConstants() as $experience) {
            $values = $this->contributionRepository->getSalaryByExperienceYear(
                $job,
                $start,
                $end,
                $experience,
                $limit,
                $isFree
            );

            if (empty($values)) {
                continue;
            }

            $values = self::extraArrayValuesFromIndex(self::filterExtremesNumbers($values));
            $labels = [];

            if (empty($values)) {
                continue;
            }

            $mean = (int) Average::mean($values);
            $min = $values[0];
            $max = $values[\count($values) - 1];

            $counts = [];
            $range = ($max - $min) / 10;

            for ($i = 0; $i < 10; ++$i) {
                $rangesStart = $min + $i * $range;
                $rangeEnd = $rangesStart + $range;

                if (0 !== $i) {
                    ++$rangesStart;
                }

                $labels[] = Numbers::formatRangeCurrency($rangesStart, $rangeEnd);
                $counts[] = \count(
                    array_filter(
                        $values,
                        static function (int $value) use ($i, $rangesStart, $rangeEnd) {
                            if (9 === $i) {
                                return $rangesStart <= $value;
                            }

                            return $rangesStart <= $value && $rangeEnd >= $value;
                        }
                    )
                );
            }

            $data[$experience] = [
                'average' => $mean,
                'formattedAverage' => Numbers::formatCurrency($mean),
                'min' => $min,
                'formattedMin' => Numbers::formatCurrency($min),
                'max' => $max,
                'formattedMax' => Numbers::formatCurrency($max),
                'data' => $counts,
                'labels' => $labels,
            ];
        }

        if (empty($data)) {
            return null;
        }

        return $data;
    }

    private function createSalaryExperienceLocationDistribution(Job $job, \DateTimeInterface $start, \DateTimeInterface $end, ?int $limit, bool $isFree): ?array
    {
        $data = [
            'columns' => array_values(Location::getConstants()),
            'lines' => [],
        ];

        foreach (ExperienceYear::getConstants() as $experience) {
            $line = [
                'label' => $experience,
                'data' => [],
            ];
            $hasDataForExperience = false;

            foreach (Location::getConstants() as $location) {
                $numbers = $this->contributionRepository->getSalaryByExperienceYearLocation(
                    $job,
                    $start,
                    $end,
                    $experience,
                    $location,
                    $limit,
                    $isFree
                );

                $formatSalaries = $isFree
                    ? $this->formatSalaryExperienceLocationDistributionFree($numbers)
                    : $this->formatSalaryExperienceLocationDistributionWork($numbers)
                ;

                if (null !== $formatSalaries) {
                    $hasDataForExperience = true;
                }

                $line['data'][] = $formatSalaries;
            }

            if (true === $hasDataForExperience) {
                $data['lines'][] = $line;
            }
        }

        if (empty($data['lines'])) {
            return null;
        }

        return $data;
    }

    private function formatSalaryExperienceLocationDistributionFree(array $values): ?string
    {
        if (empty($values)) {
            return null;
        }

        $values = self::filterExtremesNumbers($values);

        if (empty($values)) {
            return null;
        }

        return Numbers::formatCurrency((int) Average::mean(self::extraArrayValuesFromIndex($values)));
    }

    private function formatSalaryExperienceLocationDistributionWork(array $values): ?array
    {
        if (empty($values)) {
            return null;
        }

        $values = self::filterExtremesNumbers($values);

        if (empty($values)) {
            return null;
        }

        return [
            'variableSalaryRate' => round(
                Average::mean(self::extraArrayValuesFromIndex($values, 'variable')) / Average::mean(self::extraArrayValuesFromIndex($values)),
                2
            ),
            'salaryWithVariable' => Numbers::formatCurrency((int) Average::mean(self::extraArrayValuesFromIndex($values))),
        ];
    }

    private static function filterExtremesNumbers(array $values): array
    {
        $numbers = self::extraArrayValuesFromIndex($values);
        $percentile5 = (int) Descriptive::percentile($numbers, 5);
        $percentile95 = (int) Descriptive::percentile($numbers, 95);

        return array_values(
            array_filter($values, static function (array $data) use ($percentile5, $percentile95) {
                return $percentile5 <= $data['value'] && $percentile95 >= $data['value'];
            })
        );
    }

    /**
     * @return int[]
     */
    private static function extraArrayValuesFromIndex(array $values, string $indexName = 'value'): array
    {
        return array_map(static fn ($data): int => $data[$indexName], $values);
    }

    private function createAverageSalaryByEmployer(Job $job, \DateTimeInterface $start, \DateTimeInterface $end, array $employerValues, ?int $limit, bool $isFree): ?int
    {
        $values = $this->contributionRepository->getSalariesByEmployer($job, $start, $end, $isFree, $employerValues, $limit);

        if (empty($values)) {
            return null;
        }

        $values = self::extraArrayValuesFromIndex(self::filterExtremesNumbers($values));

        if (empty($values)) {
            return null;
        }

        return (int) Average::mean($values);
    }

    private function createAverageSalaryByFoundBy(Job $job, \DateTimeInterface $start, \DateTimeInterface $end, string $foundBy, ?int $limit, bool $isFree): ?int
    {
        $values = $this->contributionRepository->getSalariesByFoundBy($job, $start, $end, $isFree, $foundBy, $limit);

        if (empty($values)) {
            return null;
        }

        $values = self::extraArrayValuesFromIndex(self::filterExtremesNumbers($values));

        if (empty($values)) {
            return null;
        }

        return (int) Average::mean($values);
    }

    private function createAverageSearchJobDuration(Job $job, \DateTimeInterface $start, \DateTimeInterface $end, ?int $limit, bool $isFree): ?int
    {
        $values = $this->contributionRepository->getSearchJobDuration($job, $start, $end, $limit, $isFree);

        if (empty($values)) {
            return null;
        }

        $values = self::extraArrayValuesFromIndex(self::filterExtremesNumbers($values));

        if (empty($values)) {
            return null;
        }

        return (int) Average::mean($values);
    }
}
