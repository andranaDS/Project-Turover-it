<?php

namespace App\Forum\Repository;

use App\Forum\Entity\ForumCategory;
use App\Forum\Entity\ForumTopic;
use App\User\Contracts\UserInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ForumTopic|null find($id, $lockMode = null, $lockVersion = null)
 * @method ForumTopic|null findOneBy(array $criteria, array $orderBy = null)
 * @method ForumTopic[]    findAll()
 * @method ForumTopic[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ForumTopicRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ForumTopic::class);
    }

    public function countByCategory(ForumCategory $category): int
    {
        return (int) $this->createQueryBuilder('t')
            ->select('COUNT(t)')
            ->where('t.category = :category')
            ->setParameter('category', $category)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function getViewsCount(?ForumCategory $category = null): array
    {
        $qb = $this->createQueryBuilder('t')
            ->select('t.id, t.viewsCount')
        ;

        if (null !== $category) {
            $qb->where('t.category = :category')
                ->setParameter('category', $category)
            ;
        }

        return $qb->getQuery()
            ->getArrayResult()
        ;
    }

    public function findParticipations(UserInterface $user): array
    {
        return $this->createQueryBuilder('ft')
            ->select('DISTINCT(ft.id) as topicId')
            ->join('ft.posts', 'fp_p')
            ->where('fp_p.author = :user')
            ->andWhere('fp_p.deletedAt IS NULL')
            ->setParameter('user', $user)
            ->orderBy('ft.id', Criteria::ASC)
            ->getQuery()
            ->getResult()
        ;
    }

    public function countAll(?\DateTime $start = null, ?\DateTime $end = null): int
    {
        $qb = $this->createQueryBuilder('ft')->select('COUNT(ft)');

        if (null !== $start && null !== $end) {
            $qb
                ->andWhere('ft.createdAt BETWEEN :start AND :end')
                ->setParameter('start', $start)
                ->setParameter('end', $end)
            ;
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function countActiveUsers(): int
    {
        return (int) $this->createQueryBuilder('ft')
            ->select('COUNT(ft)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
