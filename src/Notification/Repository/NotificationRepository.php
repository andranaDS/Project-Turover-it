<?php

namespace App\Notification\Repository;

use App\Notification\Entity\Notification;
use App\Recruiter\Entity\Recruiter;
use Carbon\Carbon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Notification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notification[]    findAll()
 * @method Notification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    public function markNotificationsAsRead(Recruiter $recruiter): int
    {
        return $this->_em->createQueryBuilder()
            ->update($this->_entityName, 'n')
            ->set('n.readAt', ':readAt')
            ->where('n.recruiter = :recruiter')
            ->andWhere('n.readAt IS NULL')
            ->setParameters([
                'readAt' => Carbon::now(),
                'recruiter' => $recruiter,
            ])
            ->getQuery()
            ->execute()
        ;
    }

    public function countUnreadNotifications(Recruiter $recruiter): int
    {
        return (int) $this->createQueryBuilder('n')
            ->select('COUNT(n)')
            ->where('n.recruiter = :recruiter')
            ->andWhere('n.readAt IS NULL')
            ->setParameter('recruiter', $recruiter)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
