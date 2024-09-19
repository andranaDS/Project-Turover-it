<?php

namespace App\Forum\Repository;

use App\Forum\Entity\ForumTopicTrace;
use App\User\Contracts\UserInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ForumTopicTrace|null find($id, $lockMode = null, $lockVersion = null)
 * @method ForumTopicTrace|null findOneBy(array $criteria, array $orderBy = null)
 * @method ForumTopicTrace[]    findAll()
 * @method ForumTopicTrace[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ForumTopicTraceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ForumTopicTrace::class);
    }

    public function findLastByUser(UserInterface $user): array
    {
        return $this->createQueryBuilder('ftt')
            ->where('ftt.user = :user')
            ->andWhere('ftt.readAt IS NOT NULL')
            ->andWhere('ftt.last = true')
            ->setParameter('user', $user)
            ->orderBy('ftt.topicId', Criteria::ASC)
            ->getQuery()
            ->getResult()
        ;
    }
}
