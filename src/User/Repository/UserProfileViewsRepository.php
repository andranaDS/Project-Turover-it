<?php

namespace App\User\Repository;

use App\User\Entity\User;
use App\User\Entity\UserProfileViews;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserProfileViews|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserProfileViews|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserProfileViews[]    findAll()
 * @method UserProfileViews[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserProfileViewsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserProfileViews::class);
    }

    public function sumSince(User $user, \DateTime $date): ?string
    {
        return $this->createQueryBuilder('upv')
            ->select('SUM(upv.count)')
            ->where('upv.user = :user')
            ->andWhere('upv.date >= :date')
            ->setParameter('user', $user)
            ->setParameter('date', $date)
            ->getQuery()
            ->enableResultCache(300)
            ->getSingleScalarResult()
        ;
    }
}
