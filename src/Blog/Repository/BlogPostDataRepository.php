<?php

namespace App\Blog\Repository;

use App\Blog\Entity\BlogPostData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BlogPostData|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlogPostData|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlogPostData[]    findAll()
 * @method BlogPostData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogPostDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlogPostData::class);
    }
}
