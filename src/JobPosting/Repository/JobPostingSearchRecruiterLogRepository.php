<?php

namespace App\JobPosting\Repository;

use App\JobPosting\Entity\JobPostingSearchRecruiterLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method JobPostingSearchRecruiterLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method JobPostingSearchRecruiterLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method JobPostingSearchRecruiterLog[]    findAll()
 * @method JobPostingSearchRecruiterLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobPostingSearchRecruiterLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JobPostingSearchRecruiterLog::class);
    }
}
