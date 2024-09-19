<?php

namespace App\Recruiter\Repository;

use App\Recruiter\Entity\RecruiterNotification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RecruiterNotification|null find($id, $lockMode = null, $lockVersion = null)
 * @method RecruiterNotification|null findOneBy(array $criteria, array $orderBy = null)
 * @method RecruiterNotification[]    findAll()
 * @method RecruiterNotification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecruiterNotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RecruiterNotification::class);
    }
}
