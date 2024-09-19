<?php

namespace App\User\Repository;

use App\User\Contracts\UserInterface;
use App\User\Entity\UserSkill;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserSkill|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserSkill|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserSkill[]    findAll()
 * @method UserSkill[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserSkillRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserSkill::class);
    }

    public function countForTrend(\DateTime $start, \DateTime $end): array
    {
        return $this->createQueryBuilder('us')
            ->select('s.id as skill, COUNT(us) as count')
            ->join('us.user', 'u')
            ->join('u.data', 'u_d')
            ->join('us.skill', 's')
            ->andWhere('u.deletedAt IS NULL')
            ->andWhere('u.enabled = true')
            ->andWhere('u.locked = false')
            ->andWhere('u_d.lastActivityAt BETWEEN :start and :end')
            ->andWhere('s.displayed = 1')
            ->setParameters([
                'start' => $start,
                'end' => $end,
            ])
            ->groupBy('s.id')
            ->orderBy('count', Criteria::DESC)
            ->getQuery()
            ->getArrayResult()
        ;
    }

    public function findForSuggested(UserInterface $user): array
    {
        return $this->createQueryBuilder('us')
            ->select('us_s.slug')
            ->join('us.skill', 'us_s')
            ->where('us.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleColumnResult()
        ;
    }
}
