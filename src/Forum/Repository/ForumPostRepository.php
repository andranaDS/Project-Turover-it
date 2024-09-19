<?php

namespace App\Forum\Repository;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;
use App\Forum\Entity\ForumCategory;
use App\Forum\Entity\ForumPost;
use App\Forum\Entity\ForumTopic;
use App\User\Contracts\UserInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ForumPost|null find($id, $lockMode = null, $lockVersion = null)
 * @method ForumPost|null findOneBy(array $criteria, array $orderBy = null)
 * @method ForumPost[]    findAll()
 * @method ForumPost[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ForumPostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ForumPost::class);
    }

    public function createQueryBuilderNotDeleted(string $alias): QueryBuilder
    {
        return $this->createQueryBuilder($alias)
            ->where(sprintf('%s.deletedAt IS NULL', $alias))
        ;
    }

    public function findLastPostByTopic(ForumTopic $topic): ?ForumPost
    {
        return $this->createQueryBuilderNotDeleted('p')
            ->andWhere('p.topic = :topic')
            ->setParameter('topic', $topic)
            ->orderBy('p.createdAt', Criteria::DESC)
            ->addOrderBy('p.id', Criteria::DESC)
            ->setFirstResult(0)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function countByTopic(ForumTopic $topic): int
    {
        return (int) $this->createQueryBuilderNotDeleted('p')
            ->select('COUNT(p)')
            ->andWhere('p.topic = :topic')
            ->setParameter('topic', $topic)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function findLastPostByCategory(ForumCategory $category): ?ForumPost
    {
        $topicIds = $this->_em->createQueryBuilder()
            ->select('ft.id')
            ->from(ForumTopic::class, 'ft')
            ->where('ft.category = :category')
            ->setParameter('category', $category)
            ->getQuery()
            ->getSingleColumnResult()
        ;

        return $this->createQueryBuilderNotDeleted('p')
            ->select('p')
            ->where('p.topic IN (:topicIds)')
            ->setParameter('topicIds', $topicIds)
            ->orderBy('p.createdAt', Criteria::DESC)
            ->addOrderBy('p.id', Criteria::DESC)
            ->setFirstResult(0)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function countByCategory(ForumCategory $category): int
    {
        $topicIds = $this->_em->createQueryBuilder()
            ->select('ft.id')
            ->from(ForumTopic::class, 'ft')
            ->where('ft.category = :category')
            ->setParameter('category', $category)
            ->getQuery()
            ->getSingleColumnResult()
        ;

        return (int) $this->createQueryBuilderNotDeleted('p')
            ->select('COUNT(p.id)')
            ->where('p.topic IN (:topicIds)')
            ->setParameter('topicIds', $topicIds)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function countByUser(UserInterface $user): int
    {
        return (int) $this->createQueryBuilderNotDeleted('p')
            ->select('COUNT(p)')
            ->andWhere('p.author = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function countAll(?\DateTime $start = null, ?\DateTime $end = null): int
    {
        $qb = $this->createQueryBuilderNotDeleted('p')->select('COUNT(p)');

        if (null !== $start && null !== $end) {
            $qb
                ->andWhere('p.createdAt BETWEEN :start AND :end')
                ->setParameter('start', $start)
                ->setParameter('end', $end)
            ;
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function countRecent(): int
    {
        return (int) $this->createQueryBuilderNotDeleted('p')
            ->select('COUNT(p)')
            ->andWhere('p.createdAt > :date')
            ->setParameter('date', (new \DateTime('- 1 day'))->format('Y-m-d H:i:s'))
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function findRandom(int $length = 100): array
    {
        return $this->createQueryBuilder('p')
            ->addSelect('p_a')
            ->join('p.author', 'p_a')
            ->orderBy('RAND()')
            ->setFirstResult(0)
            ->setMaxResults($length)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getTopicReplies(ForumTopic $topic, int $page = 1, int $itemsPerPage = 30, int $childrenDepth = 1): Paginator
    {
        $qb = $this->createQueryBuilder('p')
            ->addSelect('p_a')
            ->join('p.topic', 't')
            ->join('p.author', 'p_a')
            ->addOrderBy('p.createdAt', Criteria::ASC)
        ;

        // children cascade joins
        for ($i = 0; $i < $childrenDepth; ++$i) {
            // create unique aliases
            $parentPostAlias = 'p' . str_repeat('_p', $i);
            $childrenPostAlias = 'p' . str_repeat('_p', $i + 1);
            $childrenPostAuthorAlias = $childrenPostAlias . '_a';

            // add joins
            $qb->addSelect(sprintf('%s, %s', $childrenPostAlias, $childrenPostAuthorAlias))
                ->leftJoin(
                    sprintf('%s.children', $parentPostAlias),
                    $childrenPostAlias,
                    Join::WITH,
                    sprintf('%s.deletedAt IS NULL OR %s.hidden = false', $childrenPostAlias, $childrenPostAlias)
                )
                ->leftJoin(sprintf('%s.author', $childrenPostAlias), $childrenPostAuthorAlias)
                ->addOrderBy(sprintf('%s.createdAt', $childrenPostAlias), Criteria::ASC)
            ;
        }

        $qb->andWhere('p.topic = :topic')
            ->andWhere('p.parent IS NULL')
            ->andWhere('p != t.initialPost')
            ->andWhere('p.deletedAt IS NULL OR p.hidden = false')
            ->setParameters([
                'topic' => $topic,
            ])
        ;

        $query = $qb->getQuery()
            ->setFirstResult(($page - 1) * $itemsPerPage)
            ->setMaxResults($itemsPerPage)
        ;

        $doctrinePaginator = new DoctrinePaginator($query);

        return new Paginator($doctrinePaginator);
    }

    public function findOneByIdWithChildren(int $id, int $childrenDepth = 4): ?ForumPost
    {
        $qb = $this->createQueryBuilder('p')
            ->addSelect('p_a')
            ->join('p.topic', 't')
            ->join('p.author', 'p_a')
            ->addOrderBy('p.createdAt', Criteria::ASC)
        ;

        // children cascade joins
        for ($i = 0; $i < $childrenDepth; ++$i) {
            // create unique aliases
            $parentPostAlias = 'p' . str_repeat('_p', $i);
            $childrenPostAlias = 'p' . str_repeat('_p', $i + 1);
            $childrenPostAuthorAlias = $childrenPostAlias . '_a';

            // add joins
            $qb->addSelect(sprintf('%s, %s', $childrenPostAlias, $childrenPostAuthorAlias))
                ->leftJoin(sprintf('%s.children', $parentPostAlias), $childrenPostAlias)
                ->leftJoin(sprintf('%s.author', $childrenPostAlias), $childrenPostAuthorAlias)
                ->addOrderBy(sprintf('%s.createdAt', $childrenPostAlias), Criteria::ASC)
                ->andWhere(sprintf('%s.deletedAt IS NULL OR %s.hidden = false', $childrenPostAlias, $childrenPostAlias))
            ;
        }

        return $qb->andWhere('p.id = :id')
            ->andWhere('p.deletedAt IS NULL OR p.hidden = false')
            ->setParameters([
                'id' => $id,
            ])
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function countBefore(ForumTopic $topic, ForumPost $root): int
    {
        $qb = $this->createQueryBuilder('p')
            ->select('COUNT(p)')
            ->join('p.topic', 't')
            ->addOrderBy('p.createdAt', Criteria::ASC)
        ;

        $qb->andWhere('p.topic = :topic')
            ->andWhere('p.parent IS NULL')
            ->andWhere('p != t.initialPost')
            ->andWhere('p.deletedAt IS NULL OR p.hidden = false')
            ->andWhere('p.createdAt <= :rootCreatedAt')
            ->setParameters([
                'topic' => $topic,
                'rootCreatedAt' => $root->getCreatedAt(),
            ])
        ;

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function countContributorsByDateInterval(\DateTime $start, \DateTime $end): int
    {
        return (int) $this->createQueryBuilder('fp')
            ->select('count(distinct(fp.author))')
            ->where('fp.createdAt BETWEEN :start AND :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function countNewContributorsByDateInterval(\DateTime $start, \DateTime $end): int
    {
        $oldContributors = $this->createQueryBuilder('fp')
            ->select('distinct(fp.author)')
            ->where('fp.createdAt < :start')
            ->setParameter('start', $start)
            ->getQuery()
            ->getSingleColumnResult()
        ;

        $recentContributors = $this->createQueryBuilder('fp')
            ->select('distinct(fp.author)')
            ->where('fp.createdAt BETWEEN :start AND :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getSingleColumnResult()
        ;

        return \count(array_diff($recentContributors, $oldContributors));
    }
}
