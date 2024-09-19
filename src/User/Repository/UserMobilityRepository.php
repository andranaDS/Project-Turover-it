<?php

namespace App\User\Repository;

use App\User\Entity\UserMobility;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method UserMobility|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserMobility|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserMobility[]    findAll()
 * @method UserMobility[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserMobilityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserMobility::class);
    }

    public function findForSuggested(UserInterface $user): array
    {
        return $this->createQueryBuilder('um')
            ->where('um.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult()
        ;
    }
}
