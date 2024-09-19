<?php

namespace App\Forum\Repository;

use App\Forum\Entity\ForumPost;
use App\Forum\Entity\ForumPostUpvote;
use App\Forum\Entity\ForumTopic;
use App\User\Contracts\UserInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ForumPostUpvote|null find($id, $lockMode = null, $lockVersion = null)
 * @method ForumPostUpvote|null findOneBy(array $criteria, array $orderBy = null)
 * @method ForumPostUpvote[]    findAll()
 * @method ForumPostUpvote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ForumPostUpvoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ForumPostUpvote::class);
    }

    public function createQueryBuilderPostNotDeleted(string $alias): QueryBuilder
    {
        return $this->createQueryBuilder($alias)
            ->join(sprintf('%s.post', $alias), $alias . '_p')
            ->where(sprintf('%s.deletedAt IS NULL', $alias . '_p'))
        ;
    }

    public function countByPost(ForumPost $post): int
    {
        return (int) $this->createQueryBuilderPostNotDeleted('fpu')
            ->select('COUNT(fpu)')
            ->where('fpu.post = :post')
            ->setParameter('post', $post)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function findPostIdByUser(UserInterface $user): array
    {
        return $this->createQueryBuilderPostNotDeleted('fpu')
            ->select('DISTINCT(fpu_p.id) as postId')
            ->andWhere('fpu.user = :user')
            ->setParameter('user', $user)
            ->orderBy('fpu_p.id', Criteria::ASC)
            ->getQuery()
            ->getResult()
        ;
    }

    public function countByUser(UserInterface $user): int
    {
        return (int) $this->createQueryBuilderPostNotDeleted('fpu')
            ->select('COUNT(fpu)')
            ->andWhere('fpu.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function countByTopic(ForumTopic $topic): int
    {
        return (int) $this->createQueryBuilderPostNotDeleted('fpu')
            ->select('COUNT(fpu)')
            ->andWhere('fpu_p.topic = :topic')
            ->setParameter('topic', $topic)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
