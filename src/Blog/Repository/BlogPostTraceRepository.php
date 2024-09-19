<?php

namespace App\Blog\Repository;

use App\Blog\Entity\BlogPost;
use App\Blog\Entity\BlogPostTrace;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BlogPostTrace|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlogPostTrace|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlogPostTrace[]    findAll()
 * @method BlogPostTrace[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogPostTraceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlogPostTrace::class);
    }

    public function countRecent(BlogPost $post): int
    {
        $lastTwoWeeks = (new \DateTime())->modify('-14 days');

        return (int) $this->createQueryBuilder('bpt')
            ->select('COUNT(bpt) as count')
            ->where('bpt.readAt > :from')
            ->andWhere('bpt.post = :post')
            ->setParameters([
                'from' => $lastTwoWeeks,
                'post' => $post,
            ])
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
