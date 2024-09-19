<?php

namespace App\User\Repository;

use App\User\Entity\UserJob;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserJob|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserJob|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserJob[]    findAll()
 * @method UserJob[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserJobRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserJob::class);
    }

    public function countForTrend(\DateTime $start, \DateTime $end): array
    {
        return $this->createQueryBuilder('uj')
            ->select('uj_j.id as job, COUNT(uj_u.id) AS count')
            ->join('uj.job', 'uj_j')
            ->join('uj.user', 'uj_u')
            ->join('uj_u.data', 'uj_u_d')
            ->andWhere('uj_u.deletedAt IS NULL')
            ->andWhere('uj_u.enabled = true')
            ->andWhere('uj_u.locked = false')
            ->andWhere('uj_u_d.lastActivityAt > :start')
            ->andWhere('uj_u_d.lastActivityAt < :end')
            ->setParameters([
                'start' => $start,
                'end' => $end,
            ])
            ->groupBy('uj_j.id')
            ->orderBy('count', Criteria::DESC)
            ->getQuery()
            ->getArrayResult()
        ;
    }
}
