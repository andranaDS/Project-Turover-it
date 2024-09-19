<?php

namespace App\Messaging\Repository;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;
use App\Messaging\Entity\Feed;
use App\User\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Feed|null find($id, $lockMode = null, $lockVersion = null)
 * @method Feed|null findOneBy(array $criteria, array $orderBy = null)
 * @method Feed[]    findAll()
 * @method Feed[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FeedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Feed::class);
    }

    public function createQueryBuilderOrderBase(string $alias, ?UserInterface $user, ?string $q = ''): QueryBuilder
    {
        $qb = $this->createQueryBuilder($alias)
            ->select(sprintf('%s', $alias))
            ->leftJoin(sprintf('%s.feedUsers', $alias), 'fu')
            ->where('fu.user = :user')
            ->setParameter('user', $user)
        ;

        if ($q) {
            $qb->andWhere('( m.content LIKE :q OR a.nickname LIKE LOWER(:q) OR a.firstName LIKE LOWER(:q) OR a.lastName LIKE LOWER(:q))')
                ->setParameter('q', '%' . $q . '%')
            ;
        }

        return $qb;
    }

    public function findUserFeedsOrderUnread(?UserInterface $user, int $page = 1, int $itemsPerPage = 30, ?string $q = ''): Paginator
    {
        $qb = $this->createQueryBuilderOrderBase('f', $user, $q)
            ->addSelect('CASE WHEN fu.viewAt > lm.createdAt THEN 0 ELSE 1 END AS HIDDEN custom_order')
            ->leftJoin('f.lastMessage', 'lm')
            ->leftJoin('f.messages', 'm')
            ->leftJoin('m.author', 'a')
            ->orderBy('custom_order', Criteria::DESC)
            ->addOrderBy('lm.createdAt', Criteria::DESC)
        ;

        $query = $qb->getQuery()
            ->setFirstResult(($page - 1) * $itemsPerPage)
            ->setMaxResults($itemsPerPage)
        ;

        $doctrinePaginator = new DoctrinePaginator($query);

        return new Paginator($doctrinePaginator);
    }

    public function findUserFeedsOrderFavorite(?UserInterface $user, int $page = 1, int $itemsPerPage = 30, ?string $q = ''): Paginator
    {
        $qb = $this->createQueryBuilderOrderBase('f', $user, $q)
            ->select('f')
            ->leftJoin('f.lastMessage', 'lm')
            ->orderBy('fu.favorite', Criteria::DESC)
            ->addOrderBy('lm.createdAt', Criteria::DESC)
        ;

        $query = $qb->getQuery()
            ->setFirstResult(($page - 1) * $itemsPerPage)
            ->setMaxResults($itemsPerPage)
        ;

        $doctrinePaginator = new DoctrinePaginator($query);

        return new Paginator($doctrinePaginator);
    }

    public function findOneFeedBetween(?UserInterface $author, ?UserInterface $user): ?Feed
    {
        return $this->createQueryBuilder('f')
            ->select('f')
            ->leftJoin('f.feedUsers', 'fu1')
            ->leftJoin('f.feedUsers', 'fu2')
            ->where('fu1.user = :author')
            ->andWhere('fu2.user = :user')
            ->setParameters([
                'author' => $author,
                'user' => $user,
            ])
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function countUnread(User $user): int
    {
        return $this->createQueryBuilder('f')
            ->select('count(f.id)')
            ->join('f.feedUsers', 'fu')
            ->join('f.lastMessage', 'lm')
            ->where('fu.user = :user')
            ->andWhere('fu.viewAt < lm.createdAt')
            ->andWhere('lm.author != :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
