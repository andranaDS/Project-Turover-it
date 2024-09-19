<?php

namespace App\User\Repository;

use App\User\Entity\UserShare;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserShare|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserShare|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserShare[]    findAll()
 * @method UserShare[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserShareRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserShare::class);
    }
}
