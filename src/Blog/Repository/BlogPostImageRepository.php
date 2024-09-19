<?php

namespace App\Blog\Repository;

use App\Blog\Entity\BlogPostImage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BlogPostImage|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlogPostImage|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlogPostImage[]    findAll()
 * @method BlogPostImage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogPostImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlogPostImage::class);
    }
}
