<?php

namespace App\User\Repository;

use App\User\Entity\MailjetUnsubscribeLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MailjetUnsubscribeLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method MailjetUnsubscribeLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method MailjetUnsubscribeLog[]    findAll()
 * @method MailjetUnsubscribeLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MailjetUnsubscribeLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MailjetUnsubscribeLog::class);
    }
}
