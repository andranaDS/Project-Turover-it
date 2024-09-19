<?php

namespace App\Resource\Repository;

use App\Resource\Entity\Trend;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Trend|null find($id, $lockMode = null, $lockVersion = null)
 * @method Trend|null findOneBy(array $criteria, array $orderBy = null)
 * @method Trend[]    findAll()
 * @method Trend[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrendRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trend::class);
    }

    public function findLastWithData(): ?Trend
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.date', Criteria::DESC)
            ->setFirstResult(0)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOneByDateWithData(string $date): ?Trend
    {
        return $this->createQueryBuilder('t')
            ->where('t.date = :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
