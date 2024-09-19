<?php

namespace App\Resource\Repository;

use App\Core\Entity\Job;
use App\JobPosting\Enum\Contract;
use App\Resource\Entity\Contribution;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Contribution|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contribution|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contribution[]    findAll()
 * @method Contribution[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContributionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contribution::class);
    }

    public function getPreviousFromContribution(Contribution $contribution): ?Contribution
    {
        return $this->createQueryBuilder('c')
            ->where('c.createdAt < :createdAt')
            ->orderBy('c.createdAt', Criteria::DESC)
            ->addOrderBy('c.id', Criteria::DESC)
            ->setParameter('createdAt', $contribution->getCreatedAt())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function getNextFromContribution(Contribution $contribution): ?Contribution
    {
        return $this->createQueryBuilder('c')
            ->where('c.createdAt > :createdAt')
            ->orderBy('c.createdAt', Criteria::ASC)
            ->addOrderBy('c.id', Criteria::ASC)
            ->setParameter('createdAt', $contribution->getCreatedAt())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function getSalaryByExperienceYear(
        Job $job,
        \DateTimeInterface $start,
        \DateTimeInterface $end,
        string $experienceYear,
        ?int $limit,
        bool $isFree
    ): array {
        $query = $this->createQueryBuilder('c');

        if (true === $isFree) {
            $query->select('c.dailySalary as value');
        } else {
            $query->select('(c.annualSalary + c.variableAnnualSalary) as value');
        }

        $query->orderBy('value', Criteria::ASC);

        self::withDateIntervalQueryPart($query, $start, $end);
        self::withJobQueryPart($query, $job);
        self::withExperienceYearQueryPart($query, $experienceYear);
        self::withContractQueryPart($query, $isFree);
        self::withLimitPart($query, $limit);

        return $query->getQuery()->getResult();
    }

    public function getSalaryByExperienceYearLocation(
        Job $job,
        \DateTimeInterface $start,
        \DateTimeInterface $end,
        string $experienceYear,
        string $location,
        ?int $limit,
        bool $isFree
    ): array {
        $query = $this->createQueryBuilder('c');

        if (true === $isFree) {
            $query
                ->select('c.dailySalary as value')
                ->where('c.dailySalary IS NOT NULL')
            ;
        } else {
            $query
                ->select('(c.annualSalary + c.variableAnnualSalary) as value, c.variableAnnualSalary as variable')
                ->where('c.annualSalary IS NOT NULL')
            ;
        }

        $query->orderBy('value', Criteria::ASC);

        self::withDateIntervalQueryPart($query, $start, $end);
        self::withJobQueryPart($query, $job);
        self::withExperienceYearQueryPart($query, $experienceYear);
        self::withLocationQueryPart($query, $location);
        self::withLimitPart($query, $limit);

        return $query->getQuery()->getResult();
    }

    public function getSalariesByEmployer(Job $job, \DateTimeInterface $start, \DateTimeInterface $end, bool $isFree, array $employerValues, ?int $limit): array
    {
        $query = $this->createQueryBuilder('c')
            ->select('c.annualSalary as value')
            ->where('c.employer IN (:employer)')
            ->setParameter('employer', $employerValues)
            ->orderBy('value', Criteria::ASC)
        ;

        self::withDateIntervalQueryPart($query, $start, $end);
        self::withJobQueryPart($query, $job);
        self::withContractQueryPart($query, $isFree);
        self::withLimitPart($query, $limit);

        return $query->getQuery()->getResult();
    }

    public function getSalariesByFoundBy(Job $job, \DateTimeInterface $start, \DateTimeInterface $end, bool $isFree, string $foundBy, ?int $limit): array
    {
        $query = $this->createQueryBuilder('c')
            ->select('c.dailySalary as value')
            ->where('c.foundBy IN (:foundBy)')
            ->setParameter('foundBy', $foundBy)
            ->orderBy('value', Criteria::ASC)
        ;

        self::withDateIntervalQueryPart($query, $start, $end);
        self::withJobQueryPart($query, $job);
        self::withContractQueryPart($query, $isFree);
        self::withLimitPart($query, $limit);

        return $query->getQuery()->getResult();
    }

    public function getSearchJobDuration(Job $job, \DateTimeInterface $start, \DateTimeInterface $end, ?int $limit, bool $isFree): array
    {
        $query = $this->createQueryBuilder('c')
            ->select('c.searchJobDuration as value')
            ->where('c.searchJobDuration IS NOT NULL')
        ;

        self::withDateIntervalQueryPart($query, $start, $end);
        self::withJobQueryPart($query, $job);
        self::withContractQueryPart($query, $isFree);
        self::withLimitPart($query, $limit);

        return $query->getQuery()->getResult();
    }

    public function countRemoteDaysPerWeek(Job $job, \DateTimeInterface $start, \DateTimeInterface $end, ?int $limit, bool $isFree): array
    {
        $query = $this->createQueryBuilder('c')
            ->select('c.remoteDaysPerWeek as value, count(c) as count')
            ->where('c.remoteDaysPerWeek IS NOT NULL')
            ->groupBy('value')
        ;

        self::withDateIntervalQueryPart($query, $start, $end);
        self::withJobQueryPart($query, $job);
        self::withContractQueryPart($query, $isFree);
        self::withLimitPart($query, $limit);

        return $query->getQuery()->getResult();
    }

    public function countExperienceYear(Job $job, \DateTimeInterface $start, \DateTimeInterface $end, ?int $limit, bool $isFree): array
    {
        $query = $this->createQueryBuilder('c')
            ->select('c.experienceYear as value, count(c) as count')
            ->groupBy('value')
        ;

        self::withDateIntervalQueryPart($query, $start, $end);
        self::withJobQueryPart($query, $job);
        self::withContractQueryPart($query, $isFree);
        self::withLimitPart($query, $limit);

        return $query->getQuery()->getResult();
    }

    public function countEmployer(Job $job, \DateTimeInterface $start, \DateTimeInterface $end, ?int $limit, bool $isFree): array
    {
        $query = $this->createQueryBuilder('c')
            ->select('c.employer as value, count(c) as count')
            ->where('c.employer IS NOT NULL')
            ->groupBy('value')
        ;

        self::withDateIntervalQueryPart($query, $start, $end);
        self::withJobQueryPart($query, $job);
        self::withContractQueryPart($query, $isFree);
        self::withLimitPart($query, $limit);

        return $query->getQuery()->getResult();
    }

    public function countFoundBy(Job $job, \DateTimeInterface $start, \DateTimeInterface $end, ?int $limit, bool $isFree): array
    {
        $query = $this->createQueryBuilder('c')
            ->select('c.foundBy as value, count(c) as count')
            ->groupBy('value')
        ;

        self::withDateIntervalQueryPart($query, $start, $end);
        self::withJobQueryPart($query, $job);
        self::withContractQueryPart($query, $isFree);
        self::withLimitPart($query, $limit);

        return $query->getQuery()->getResult();
    }

    public function countContractDuration(Job $job, \DateTimeInterface $start, \DateTimeInterface $end, ?int $limit, bool $isFree): array
    {
        $query = $this->createQueryBuilder('c')
            ->select('c.contractDuration as value, count(c) as count')
            ->where('c.contractDuration IS NOT NULL')
            ->groupBy('value')
        ;

        self::withDateIntervalQueryPart($query, $start, $end);
        self::withJobQueryPart($query, $job);
        self::withContractQueryPart($query, $isFree);
        self::withLimitPart($query, $limit);

        return $query->getQuery()->getResult();
    }

    public function countContract(Job $job, \DateTimeInterface $start, \DateTimeInterface $end, ?int $limit, bool $isFree): array
    {
        $query = $this->createQueryBuilder('c')
            ->select('c.contract as value, count(c) as count')
            ->groupBy('value')
        ;

        self::withDateIntervalQueryPart($query, $start, $end);
        self::withJobQueryPart($query, $job);
        self::withContractQueryPart($query, $isFree);
        self::withLimitPart($query, $limit);

        return $query->getQuery()->getResult();
    }

    public function countOnCall(Job $job, \DateTimeInterface $start, \DateTimeInterface $end, ?int $limit, bool $isFree): array
    {
        $query = $this->createQueryBuilder('c')
            ->select('c.onCall as value, count(c) as count')
            ->where('c.onCall IS NOT NULL')
            ->groupBy('value')
        ;

        self::withDateIntervalQueryPart($query, $start, $end);
        self::withJobQueryPart($query, $job);
        self::withContractQueryPart($query, $isFree);
        self::withLimitPart($query, $limit);

        return $query->getQuery()->getResult();
    }

    protected static function withDateIntervalQueryPart(QueryBuilder $queryBuilder, \DateTimeInterface $start, \DateTimeInterface $end): void
    {
        /*
            Disable temporarily for DEMO
            $queryBuilder
                ->andWhere('c.createdAt BETWEEN :end AND :start')
                ->setParameter('start', $start)
                ->setParameter('end', $end)
            ;
        */
    }

    protected static function withJobQueryPart(QueryBuilder $queryBuilder, Job $job): void
    {
        $queryBuilder
            ->andWhere('c.job = :job')
            ->setParameter('job', $job)
        ;
    }

    protected static function withExperienceYearQueryPart(QueryBuilder $queryBuilder, string $experienceYear): void
    {
        $queryBuilder
            ->andWhere('c.experienceYear = :experienceYear')
            ->setParameter('experienceYear', $experienceYear)
        ;
    }

    protected static function withLocationQueryPart(QueryBuilder $queryBuilder, string $location): void
    {
        $queryBuilder
            ->andWhere('c.location = :location')
            ->setParameter('location', $location)
        ;
    }

    protected static function withContractQueryPart(QueryBuilder $queryBuilder, bool $isFree): void
    {
        $contractValues = $isFree ? Contract::getFreeValues() : Contract::getWorkValues();
        $queryBuilder
            ->andWhere('c.contract IN (:contractValues)')
            ->setParameter('contractValues', $contractValues)
        ;
    }

    protected static function withLimitPart(QueryBuilder $queryBuilder, ?int $limit): void
    {
        if (null !== $limit) {
            $queryBuilder->setMaxResults($limit);
        }
    }
}
