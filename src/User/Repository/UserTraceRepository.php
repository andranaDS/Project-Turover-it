<?php

namespace App\User\Repository;

use App\User\Entity\User;
use App\User\Entity\UserTrace;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserTrace|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserTrace|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserTrace[]    findAll()
 * @method UserTrace[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserTraceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserTrace::class);
    }

    /**
     * Count view users.
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countByUser(User $user): int
    {
        return $this->createQueryBuilder('ut')
            ->select('COUNT(ut)')
            ->andWhere('ut.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
