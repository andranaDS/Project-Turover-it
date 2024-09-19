<?php

namespace App\JobPosting\Repository;

use App\JobPosting\Entity\JobPostingSearchRecruiterFavorite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method JobPostingSearchRecruiterFavorite|null find($id, $lockMode = null, $lockVersion = null)
 * @method JobPostingSearchRecruiterFavorite|null findOneBy(array $criteria, array $orderBy = null)
 * @method JobPostingSearchRecruiterFavorite[]    findAll()
 * @method JobPostingSearchRecruiterFavorite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobPostingSearchRecruiterFavoriteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JobPostingSearchRecruiterFavorite::class);
    }
}
