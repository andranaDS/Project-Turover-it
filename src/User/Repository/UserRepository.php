<?php

namespace App\User\Repository;

use App\Company\Entity\Company;
use App\User\Entity\User;
use App\User\Enum\Availability;
use Carbon\Carbon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function exists(int $id): bool
    {
        return null !== $this->createQueryBuilder('u')
                ->select('1')
                ->where('u.id = :id')
                ->setParameter('id', $id)
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult()
        ;
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function countForumActive(): int
    {
        return (int) $this->createQueryBuilder('u')
            ->select('COUNT(u)')
            ->join('u.data', 'u_d')
            ->where('u_d.lastForumActivityAt > :date')
            ->setParameter('date', (new \DateTime('- 15 minutes'))->format('Y-m-d H:i:s'))
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function countByGender(?\DateTime $start = null, ?\DateTime $end = null): array
    {
        $qb = $this->createQueryBuilder('u')
            ->select('u.gender as value, COUNT(u) as count')
            ->join('u.data', 'u_d')
            ->andWhere('u.gender IS NOT NULL')
            ->andWhere('u.deletedAt IS NULL')
            ->andWhere('u.enabled = true')
            ->andWhere('u.locked = false')
        ;

        if (null !== $start) {
            $qb->andWhere('u_d.lastActivityAt > :start')
                ->setParameter('start', $start)
            ;
        }
        if (null !== $end) {
            $qb->andWhere('u_d.lastActivityAt < :end')
                ->setParameter('end', $end)
            ;
        }

        return $qb->groupBy('value')
            ->getQuery()
            ->getArrayResult()
        ;
    }

    public function countByRemote(?\DateTime $start = null, ?\DateTime $end = null): array
    {
        $qb = $this->createQueryBuilder('u')
            ->select("IFELSE(u.fulltimeTeleworking = true, 'true', 'false') as value, COUNT(u) as count")
            ->join('u.data', 'u_d')
            ->andWhere('u.fulltimeTeleworking IS NOT NULL')
            ->andWhere('u.deletedAt IS NULL')
            ->andWhere('u.enabled = true')
            ->andWhere('u.locked = false')
        ;

        if (null !== $start) {
            $qb->andWhere('u_d.lastActivityAt > :start')
                ->setParameter('start', $start)
            ;
        }
        if (null !== $end) {
            $qb->andWhere('u_d.lastActivityAt < :end')
                ->setParameter('end', $end)
            ;
        }

        return $qb->groupBy('value')
            ->getQuery()
            ->getArrayResult()
        ;
    }

    public function countByType(?\DateTime $start = null, ?\DateTime $end = null): array
    {
        $qb = $this->createQueryBuilder('u')
            ->select("IFELSE((u.freelance = true AND u.employee = false) OR (u.freelance = true AND u.employee = true AND u.companyRegistrationNumber IS NOT NULL), 'free', 'work') as value, count(u) as count")
            ->join('u.data', 'u_d')
            ->andWhere('u.freelance = true OR u.employee = true')
            ->andWhere('u.deletedAt IS NULL')
            ->andWhere('u.enabled = true')
            ->andWhere('u.locked = false')
        ;

        if (null !== $start) {
            $qb->andWhere('u_d.lastActivityAt > :start')
                ->setParameter('start', $start)
            ;
        }

        if (null !== $end) {
            $qb->andWhere('u_d.lastActivityAt < :end')
                ->setParameter('end', $end)
            ;
        }

        return $qb
            ->groupBy('value')
            ->getQuery()
            ->getArrayResult()
        ;
    }

    public function deleteAll(): void
    {
        $this->_em->createQueryBuilder()->delete(User::class)
            ->getQuery()
            ->execute()
        ;
    }

    public function countContributors(): int
    {
        return (int) $this->createQueryBuilder('u')
            ->select('count(u)')
            ->where('u.forumPostsCount > 0')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function findWithUncompletedProfileByDateToIterate(
        \DateTimeInterface $start,
        \DateTimeInterface $end,
        \DateTimeInterface $launchDate
    ): iterable {
        $query = $this->createQueryBuilder('u')
            ->distinct()
            ->select('u', 'ud')
            ->join('u.data', 'ud')
            ->where('u.profileCompleted = false')
            ->andWhere('u.createdAt >= :start AND u.createdAt < :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
        ;

        self::withLaunchDatePart($query, $launchDate);
        self::withNotDeletedPart($query);

        return $query->getQuery()->toIterable();
    }

    public function findAllWithNoJobPostingSearchByDateToIterate(\DateTimeInterface $date, \DateTimeInterface $launchDate): iterable
    {
        $query = $this->createQueryBuilder('u')
            ->distinct()
            ->join('u.data', 'ud')
            ->leftjoin('u.jobPostingSearches', 'jps')
            ->where('jps.id IS NULL')
            ->andWhere('u.createdAt >= :date')
            ->andWhere('ud.cronNoJobPostingSearchExecAt IS NULL')
            ->setParameter('date', $date)
        ;

        self::withLaunchDatePart($query, $launchDate);
        self::withNotDeletedPart($query);

        return $query->getQuery()->toIterable();
    }

    public function findAllWithActiveJobPostingSearchByAlertMissionDate(\DateTimeInterface $date, ?int $limit = null): array
    {
        $query = $this->createQueryBuilder('u')
            ->select('u')
            ->join('u.data', 'ud')
            ->where('ud.cronAlertMissionsExecAt IS NULL OR ud.cronAlertMissionsExecAt < :date')
            ->andWhere('u.enabled = 1')
            ->andWhere('u.activeJobPostingSearchesCount > 0')
            ->setParameter('date', $date)
        ;

        if (null !== $limit) {
            $query->setMaxResults($limit);
        }

        self::withNotDeletedPart($query);

        return $query->getQuery()->getResult();
    }

    public function findAllVisibleWIthImmediateAvailabilityByDateToIterate(\DateTimeInterface $start, \DateTimeInterface $end): iterable
    {
        $query = $this->createQueryBuilder('u')
            ->distinct()
            ->where('u.visible = 1')
            ->andWhere('u.availability = :immediate')
            ->andWhere('u.statusUpdatedAt BETWEEN :start and :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('immediate', Availability::IMMEDIATE)
        ;

        self::withNotDeletedPart($query);

        return $query->getQuery()->toIterable();
    }

    public function findAllVisibleWIthNoImmediateAvailabilityByDateToIterate(
        \DateTimeInterface $nextAvailabilityStart,
        \DateTimeInterface $nextAvailabilityEnd,
        \DateTimeInterface $statusUpdatedAt
    ): iterable {
        $query = $this->createQueryBuilder('u')
            ->distinct()
            ->where('u.visible = 1')
            ->andWhere('u.availability in (:status)')
            ->andWhere('u.nextAvailabilityAt > :start AND u.nextAvailabilityAt < :end')
            ->andWhere('u.statusUpdatedAt < :statusUpdatedAt')
            ->setParameter('start', $nextAvailabilityStart)
            ->setParameter('end', $nextAvailabilityEnd)
            ->setParameter('statusUpdatedAt', $statusUpdatedAt)
            ->setParameter('status', [
                Availability::WITHIN_1_MONTH,
                Availability::WITHIN_2_MONTH,
                Availability::WITHIN_3_MONTH,
            ])
        ;

        self::withNotDeletedPart($query);

        return $query->getQuery()->toIterable();
    }

    public function findAllVisibleWithNoAvailabilityByDateToIterate(\DateTimeInterface $start, \DateTimeInterface $end): iterable
    {
        $query = $this->createQueryBuilder('u')
            ->distinct()
            ->where('u.visible = 1')
            ->andWhere('u.availability = :none')
            ->andWhere('u.statusUpdatedAt BETWEEN :start and :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('none', Availability::NONE)
        ;

        self::withNotDeletedPart($query);

        return $query->getQuery()->toIterable();
    }

    public function findAllWithImmediateAvailabilityByLastActivityDateToIterate(
        \DateTimeInterface $start,
        \DateTimeInterface $end,
        \DateTimeInterface $updatedAt,
        bool $withVisible = false
    ): iterable {
        $query = $this->createQueryBuilder('u')
            ->distinct()
            ->join('u.data', 'u_d')
            ->where('u.availability = :immediate')
            ->andWhere('u_d.lastActivityAt BETWEEN :start and :end')
            ->andWhere('u.updatedAt < :updatedAt')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('immediate', Availability::IMMEDIATE)
            ->setParameter('updatedAt', $updatedAt)
        ;

        if (true === $withVisible) {
            $query->andWhere('u.visible = 1');
        }

        self::withNotDeletedPart($query);

        return $query->getQuery()->toIterable();
    }

    public function findAllNonVisibleByStatusDateToIterate(
        \DateTimeInterface $start,
        \DateTimeInterface $end,
        \DateTimeInterface $launchDate
    ): iterable {
        $query = $this->createQueryBuilder('u')
            ->distinct()
            ->select('u', 'ud')
            ->join('u.data', 'ud')
            ->where('u.visible = 0')
            ->andWhere('u.statusUpdatedAt >= :start AND u.statusUpdatedAt < :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
        ;

        self::withLaunchDatePart($query, $launchDate);
        self::withNotDeletedPart($query);

        return $query->getQuery()->toIterable();
    }

    public function findAllNoImmediateAvailabilityByNextAvailabilityDateToIterate(
        \DateTimeInterface $date,
        \DateTimeInterface $launchDate
    ): iterable {
        $query = $this->createQueryBuilder('u')
            ->distinct()
            ->where('u.nextAvailabilityAt <= :date')
            ->andWhere('u.availability in (:status)')
            ->setParameter('date', $date)
            ->setParameter('status', [
                Availability::WITHIN_1_MONTH,
                Availability::WITHIN_2_MONTH,
                Availability::WITHIN_3_MONTH,
                Availability::DATE,
            ])
        ;

        self::withLaunchDatePart($query, $launchDate);
        self::withNotDeletedPart($query);

        return $query->getQuery()->toIterable();
    }

    protected static function withNotDeletedPart(QueryBuilder $queryBuilder, string $alias = 'u'): void
    {
        $queryBuilder->andWhere(sprintf('%s.deletedAt IS NULL', $alias));
    }

    protected static function withLaunchDatePart(QueryBuilder $queryBuilder, \DateTimeInterface $date, string $alias = 'u'): void
    {
        $queryBuilder
            ->andWhere(sprintf('%s.createdAt >= :launchDate', $alias))
            ->setParameter('launchDate', $date)
        ;
    }

    /**
     * Condition:
     *      - User is a Employee or Employee + Freelance and its update date < now - 8 months
     *      - User is a Freelance and its update date < now - 12 months.
     */
    public function findAllToSetProfileNotCompleted(): array
    {
        $now = Carbon::now();
        $query = $this->createQueryBuilder('u');
        $orStatements = $query->expr()->orX()
            ->add('u.updatedAt < :eightMonthsAgo AND u.employee = 1')
            ->add('u.updatedAt < :twelveMonthsAgo AND (u.employee = 0 AND u.freelance = 1)')
        ;

        $query
            ->where('u.profileCompleted = 1')
            ->andWhere($orStatements)
            ->setParameter('eightMonthsAgo', $now->copy()->subMonths(8))
            ->setParameter('twelveMonthsAgo', $now->copy()->subMonths(12))
        ;

        self::withNotDeletedPart($query);

        return $query->getQuery()->getResult();
    }

    public function countByCompany(Company $company): array
    {
        return $this->createQueryBuilder('u')
            ->select('u.visible, COUNT(u) as count')
            ->join('u.createdBy', 'r')
            ->where('r.company = :company')
            ->setParameter('company', $company)
            ->groupBy('u.visible')
            ->getQuery()
            ->getResult()
        ;
    }
}
