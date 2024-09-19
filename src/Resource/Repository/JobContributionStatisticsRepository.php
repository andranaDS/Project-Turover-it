<?php

namespace App\Resource\Repository;

use App\Resource\Entity\JobContributionStatistics;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method JobContributionStatistics|null find($id, $lockMode = null, $lockVersion = null)
 * @method JobContributionStatistics|null findOneBy(array $criteria, array $orderBy = null)
 * @method JobContributionStatistics[]    findAll()
 * @method JobContributionStatistics[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobContributionStatisticsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JobContributionStatistics::class);
    }
}
