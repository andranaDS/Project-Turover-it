<?php

namespace App\JobPosting\Repository;

use App\JobPosting\Entity\JobPostingUserFavorite;
use App\User\Contracts\UserInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method JobPostingUserFavorite|null find($id, $lockMode = null, $lockVersion = null)
 * @method JobPostingUserFavorite|null findOneBy(array $criteria, array $orderBy = null)
 * @method JobPostingUserFavorite[]    findAll()
 * @method JobPostingUserFavorite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobPostingUserFavoriteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JobPostingUserFavorite::class);
    }

    public function findJobPostingIdByUser(UserInterface $user): array
    {
        return $this->createQueryBuilder('jpf')
            ->select('DISTINCT(jpf.jobPosting) as jobPostingId')
            ->where('jpf.user = :user')
            ->setParameter('user', $user)
            ->orderBy('jpf.jobPosting', Criteria::ASC)
            ->getQuery()
            ->getResult()
        ;
    }
}
