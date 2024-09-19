<?php

namespace App\User\Repository;

use App\User\Entity\BanUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BanUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method BanUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method BanUser[]    findAll()
 * @method BanUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BanUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BanUser::class);
    }
}
