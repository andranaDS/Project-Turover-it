<?php

namespace App\JobPosting\Repository;

use App\JobPosting\Entity\JobPostingSearch;
use App\User\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method JobPostingSearch|null find($id, $lockMode = null, $lockVersion = null)
 * @method JobPostingSearch|null findOneBy(array $criteria, array $orderBy = null)
 * @method JobPostingSearch[]    findAll()
 * @method JobPostingSearch[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobPostingSearchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JobPostingSearch::class);
    }

    public function findByUserWithData(User $user): array
    {
        return $this->createQueryBuilder('jps')
            ->leftJoin('jps.locations', 'jps_l')
            ->where('jps.user = :user')
            ->andWhere('jps.activeAlert = true')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult()
        ;
    }
}
