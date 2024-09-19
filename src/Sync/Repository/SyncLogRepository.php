<?php

namespace App\Sync\Repository;

use App\Sync\Entity\SyncLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SyncLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method SyncLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method SyncLog[]    findAll()
 * @method SyncLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SyncLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SyncLog::class);
    }

    public function countOldLogs(\DateTime $datetime): mixed
    {
        return $this->createQueryBuilder('sl')
            ->select('count(sl)')
            ->where('sl.createdAt < :datetime')
            ->setParameter('datetime', $datetime)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function deleteOldLogs(\DateTime $datetime): mixed
    {
        return $this->createQueryBuilder('sl')
            ->delete(SyncLog::class, 'sl')
            ->where('sl.createdAt < :datetime')
            ->setParameter('datetime', $datetime)
            ->getQuery()
            ->execute()
        ;
    }
}
