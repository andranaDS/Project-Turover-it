<?php

namespace App\Forum\Repository;

use App\Forum\Entity\ForumPostReport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ForumPostReport|null find($id, $lockMode = null, $lockVersion = null)
 * @method ForumPostReport|null findOneBy(array $criteria, array $orderBy = null)
 * @method ForumPostReport[]    findAll()
 * @method ForumPostReport[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ForumPostReportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ForumPostReport::class);
    }
}
