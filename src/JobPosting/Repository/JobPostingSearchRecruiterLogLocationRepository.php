<?php

namespace App\JobPosting\Repository;

use App\JobPosting\Entity\JobPostingSearchRecruiterLogLocation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method JobPostingSearchRecruiterLogLocation|null find($id, $lockMode = null, $lockVersion = null)
 * @method JobPostingSearchRecruiterLogLocation|null findOneBy(array $criteria, array $orderBy = null)
 * @method JobPostingSearchRecruiterLogLocation[]    findAll()
 * @method JobPostingSearchRecruiterLogLocation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobPostingSearchRecruiterLogLocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JobPostingSearchRecruiterLogLocation::class);
    }
}
