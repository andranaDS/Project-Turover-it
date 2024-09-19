<?php

namespace App\JobPosting\Repository;

use App\JobPosting\Entity\Application;
use App\JobPosting\Entity\JobPosting;
use App\JobPosting\Enum\ApplicationState;
use App\User\Contracts\UserInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Application|null find($id, $lockMode = null, $lockVersion = null)
 * @method Application|null findOneBy(array $criteria, array $orderBy = null)
 * @method Application[]    findAll()
 * @method Application[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApplicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Application::class);
    }

    public function findJobPostingIdByUser(UserInterface $user, array $steps = []): array
    {
        return $this->createQueryBuilder('a')
            ->select('DISTINCT(a.jobPosting) as jobPostingId')
            ->where('a.user = :user')
            ->andWhere('a.step IN (:steps)')
            ->andWhere('a.jobPosting IS NOT NULL')
            ->setParameters([
                'user' => $user,
                'steps' => $steps,
            ])
            ->orderBy('a.jobPosting', Criteria::ASC)
            ->getQuery()
            ->getResult()
        ;
    }

    public function countByJobPosting(JobPosting $jobPosting): int
    {
        return (int) $this->createQueryBuilder('a')
            ->select('COUNT(a)')
            ->where('a.jobPosting = :jobPosting')
            ->setParameter('jobPosting', $jobPosting)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function countByUser(UserInterface $user): int
    {
        return (int) $this->createQueryBuilder('a')
            ->select('COUNT(a)')
            ->andWhere('a.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function findToUpdate(array $data): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a')
            ->leftJoin('a.user', 'u')
            ->where('u.id = :userId')
            ->setParameter('userId', $data['userId'])
        ;

        if (\array_key_exists('jobPostingOldId', $data) && $data['jobPostingOldId']) {
            $qb->leftJoin('a.jobPosting', 'jp')
                ->andWhere('jp.oldId = :jobPostingOldId')
                ->setParameter('jobPostingOldId', $data['jobPostingOldId'])
            ;
        } elseif (\array_key_exists('companyOldId', $data) && $data['companyOldId']) {
            $qb->leftJoin('a.company', 'c')
                ->andWhere('c.oldId = :companyOldId')
                ->setParameter('companyOldId', $data['companyOldId'])
            ;
        }

        return $qb->getQuery()->getResult();
    }

    public function findInProgressWithUnpublishedJobPosting(): array
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.jobPosting', 'j')
            ->where('j.published = :false')
            ->andWhere('a.state = :inProgress')
            ->setParameter('inProgress', ApplicationState::IN_PROGRESS)
            ->setParameter('false', false)
            ->getQuery()
            ->getResult()
        ;
    }
}
