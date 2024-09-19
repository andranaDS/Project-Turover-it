<?php

namespace App\User\Repository;

use App\User\Entity\HrFlowLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method HrFlowLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method HrFlowLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method HrFlowLog[]    findAll()
 * @method HrFlowLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HrFlowLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HrFlowLog::class);
    }
}
