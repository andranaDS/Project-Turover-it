<?php

namespace App\JobPosting\Repository;

use App\JobPosting\Entity\JobPostingSearchRecruiterFavoriteLocation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method JobPostingSearchRecruiterFavoriteLocation|null find($id, $lockMode = null, $lockVersion = null)
 * @method JobPostingSearchRecruiterFavoriteLocation|null findOneBy(array $criteria, array $orderBy = null)
 * @method JobPostingSearchRecruiterFavoriteLocation[]    findAll()
 * @method JobPostingSearchRecruiterFavoriteLocation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobPostingSearchRecruiterFavoriteLocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JobPostingSearchRecruiterFavoriteLocation::class);
    }
}
