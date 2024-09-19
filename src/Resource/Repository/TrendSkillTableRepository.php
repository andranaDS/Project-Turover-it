<?php

namespace App\Resource\Repository;

use App\Resource\Entity\TrendSkillTable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrendSkillTable|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrendSkillTable|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrendSkillTable[]    findAll()
 * @method TrendSkillTable[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrendSkillTableRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrendSkillTable::class);
    }

    public function findOneByIdWithData(int $id, ?int $maxResults = null): ?TrendSkillTable
    {
        $qb = $this->createQueryBuilder('t')
            ->addSelect('t_l, t_l_s')
            ->leftJoin('t.lines', 't_l')
            ->leftJoin('t_l.skill', 't_l_s')
            ->where('t.id = :id')
            ->setParameter('id', $id)
            ->orderBy('t_l.position', Criteria::ASC)
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
