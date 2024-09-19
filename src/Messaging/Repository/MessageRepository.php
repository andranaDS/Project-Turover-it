<?php

namespace App\Messaging\Repository;

use App\Messaging\Entity\Feed;
use App\Messaging\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function findLastMessageByFeed(Feed $feed): ?Message
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.feed = :feed')
            ->setParameter('feed', $feed)
            ->orderBy('m.createdAt', Criteria::DESC)
            ->addOrderBy('m.id', Criteria::DESC)
            ->setFirstResult(0)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
