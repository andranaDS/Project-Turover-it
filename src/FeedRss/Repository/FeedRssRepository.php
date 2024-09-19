<?php

namespace App\FeedRss\Repository;

use App\FeedRss\Entity\FeedRss;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FeedRss|null find($id, $lockMode = null, $lockVersion = null)
 * @method FeedRss|null findOneBy(array $criteria, array $orderBy = null)
 * @method FeedRss[]    findAll()
 * @method FeedRss[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FeedRssRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FeedRss::class);
    }
}
