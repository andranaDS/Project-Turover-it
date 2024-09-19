<?php

namespace App\Forum\Repository;

use App\Forum\Entity\ForumTopicFavorite;
use App\User\Contracts\UserInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ForumTopicFavorite|null find($id, $lockMode = null, $lockVersion = null)
 * @method ForumTopicFavorite|null findOneBy(array $criteria, array $orderBy = null)
 * @method ForumTopicFavorite[]    findAll()
 * @method ForumTopicFavorite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ForumTopicFavoriteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ForumTopicFavorite::class);
    }

    public function findTopicIdByUser(UserInterface $user): array
    {
        return $this->createQueryBuilder('ftv')
            ->select('DISTINCT(ftv_t.id) as topicId')
            ->join('ftv.topic', 'ftv_t')
            ->where('ftv.user = :user')
            ->setParameter('user', $user)
            ->orderBy('ftv_t.id', Criteria::ASC)
            ->getQuery()
            ->getResult()
        ;
    }
}
