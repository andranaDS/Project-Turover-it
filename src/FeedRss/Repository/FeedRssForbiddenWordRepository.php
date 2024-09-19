<?php

namespace App\FeedRss\Repository;

use App\FeedRss\Entity\FeedRssForbiddenWord;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FeedRssForbiddenWord|null find($id, $lockMode = null, $lockVersion = null)
 * @method FeedRssForbiddenWord|null findOneBy(array $criteria, array $orderBy = null)
 * @method FeedRssForbiddenWord[]    findAll()
 * @method FeedRssForbiddenWord[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FeedRssForbiddenWordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FeedRssForbiddenWord::class);
    }

    public function getForbiddenWords(): array
    {
        return $this->createQueryBuilder('fw')
            ->select('fw.name')
            ->getQuery()
            ->getSingleColumnResult()
        ;
    }
}
