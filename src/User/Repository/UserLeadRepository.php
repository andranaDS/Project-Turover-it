<?php

namespace App\User\Repository;

use App\User\Entity\UserLead;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserLead|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserLead|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserLead[]    findAll()
 * @method UserLead[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserLeadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserLead::class);
    }

    public function getByDateInterval(\DateTimeInterface $start, \DateTimeInterface $end): array
    {
        return $this->createQueryBuilder('ul')
            ->where('ul.isSuccess = 1')
            ->andWhere('ul.createdAt BETWEEN :start AND :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('ul.createdAt', Criteria::ASC)
            ->getQuery()
            ->getResult()
        ;
    }
}
