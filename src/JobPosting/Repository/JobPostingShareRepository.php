<?php

namespace App\JobPosting\Repository;

use App\JobPosting\Entity\JobPostingShare;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method JobPostingShare|null find($id, $lockMode = null, $lockVersion = null)
 * @method JobPostingShare|null findOneBy(array $criteria, array $orderBy = null)
 * @method JobPostingShare[]    findAll()
 * @method JobPostingShare[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobPostingShareRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JobPostingShare::class);
    }
}
