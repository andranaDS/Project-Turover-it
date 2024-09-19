<?php

namespace App\JobPosting\Repository;

use App\JobPosting\Entity\JobPostingSearchRecruiterAlertLocation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method JobPostingSearchRecruiterAlertLocation|null find($id, $lockMode = null, $lockVersion = null)
 * @method JobPostingSearchRecruiterAlertLocation|null findOneBy(array $criteria, array $orderBy = null)
 * @method JobPostingSearchRecruiterAlertLocation[]    findAll()
 * @method JobPostingSearchRecruiterAlertLocation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobPostingSearchRecruiterAlertLocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JobPostingSearchRecruiterAlertLocation::class);
    }
}
