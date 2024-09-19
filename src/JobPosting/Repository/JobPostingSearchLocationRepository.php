<?php

namespace App\JobPosting\Repository;

use App\JobPosting\Entity\JobPostingSearchLocation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method JobPostingSearchLocation|null find($id, $lockMode = null, $lockVersion = null)
 * @method JobPostingSearchLocation|null findOneBy(array $criteria, array $orderBy = null)
 * @method JobPostingSearchLocation[]    findAll()
 * @method JobPostingSearchLocation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobPostingSearchLocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JobPostingSearchLocation::class);
    }
}
