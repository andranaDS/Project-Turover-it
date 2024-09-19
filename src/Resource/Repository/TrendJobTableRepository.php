<?php

namespace App\Resource\Repository;

use App\Resource\Entity\TrendJobTable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrendJobTable|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrendJobTable|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrendJobTable[]    findAll()
 * @method TrendJobTable[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrendJobTableRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrendJobTable::class);
    }

    public function findOneByIdWithData(int $id, ?int $maxResults = null): ?TrendJobTable
    {
        $qb = $this->createQueryBuilder('t')
            ->addSelect('t_l, t_l_j')
            ->leftJoin('t.lines', 't_l')
            ->leftJoin('t_l.job', 't_l_j')
            ->where('t.id = :id')
            ->setParameter('id', $id)
        ;

        if (null !== $maxResults) {
            $qb->setFirstResult(0)
                ->setMaxResults($maxResults)
            ;
        }

        return $qb->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
