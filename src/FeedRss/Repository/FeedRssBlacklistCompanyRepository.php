<?php

namespace App\FeedRss\Repository;

use App\FeedRss\Entity\FeedRssBlacklistCompany;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FeedRssBlacklistCompany|null find($id, $lockMode = null, $lockVersion = null)
 * @method FeedRssBlacklistCompany|null findOneBy(array $criteria, array $orderBy = null)
 * @method FeedRssBlacklistCompany[]    findAll()
 * @method FeedRssBlacklistCompany[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FeedRssBlacklistCompanyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FeedRssBlacklistCompany::class);
    }
}
