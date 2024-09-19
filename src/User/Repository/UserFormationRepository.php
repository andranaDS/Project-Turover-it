<?php

namespace App\User\Repository;

use App\User\Entity\UserFormation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserFormation|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserFormation|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserFormation[]    findAll()
 * @method UserFormation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserFormationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserFormation::class);
    }
}
