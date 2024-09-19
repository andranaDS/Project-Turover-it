<?php

namespace App\Messaging\Repository;

use App\Messaging\Entity\FeedUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FeedUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method FeedUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method FeedUser[]    findAll()
 * @method FeedUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FeedUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FeedUser::class);
    }
}
