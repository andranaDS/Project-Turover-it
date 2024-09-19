<?php

namespace App\Blog\Repository;

use App\Blog\Entity\BlogPost;
use App\Blog\Entity\BlogPostUpvote;
use App\User\Contracts\UserInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BlogPostUpvote|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlogPostUpvote|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlogPostUpvote[]    findAll()
 * @method BlogPostUpvote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogPostUpvoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlogPostUpvote::class);
    }

    public function countByPost(BlogPost $post): int
    {
        return (int) $this->createQueryBuilder('bpu')
            ->select('COUNT(bpu)')
            ->where('bpu.post = :post')
            ->setParameter('post', $post)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function findPostIdByUser(UserInterface $user): array
    {
        return $this->createQueryBuilder('bpu')
            ->select('DISTINCT(bpu_p.id) as postId')
            ->join('bpu.post', 'bpu_p')
            ->andWhere('bpu.user = :user')
            ->setParameter('user', $user)
            ->orderBy('bpu_p.id', Criteria::ASC)
            ->getQuery()
            ->getResult()
        ;
    }
}
