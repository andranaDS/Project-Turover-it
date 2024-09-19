<?php

namespace App\JobPosting\Repository;

use App\JobPosting\Entity\JobPostingUserTrace;
use App\User\Contracts\UserInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method JobPostingUserTrace|null find($id, $lockMode = null, $lockVersion = null)
 * @method JobPostingUserTrace|null findOneBy(array $criteria, array $orderBy = null)
 * @method JobPostingUserTrace[]    findAll()
 * @method JobPostingUserTrace[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobPostingUserTraceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JobPostingUserTrace::class);
    }

    public function findByJobPostingIds(array $jobPostingIds, ?int $count = null): array
    {
        $query = $this->createQueryBuilder('j')
            ->where('j.jobPosting IN (:ids)')
            ->setParameter('ids', $jobPostingIds)
            ->groupBy('j.jobPosting')
        ;

        if (null !== $count) {
            $query
                ->having('COUNT(j) >= :traceCount')
                ->setParameter('traceCount', $count)
            ;
        }

        return $query->getQuery()->getResult();
    }

    public function findJobPostingIdsByUser(UserInterface $user): array
    {
        return $this->createQueryBuilder('jpt')
            ->select('DISTINCT(jpt.jobPosting) as jobPostingId')
            ->where('jpt.user = :user')
            ->setParameters([
                'user' => $user,
            ])
            ->orderBy('jpt.jobPosting', Criteria::ASC)
            ->getQuery()
            ->getResult()
        ;
    }
}
