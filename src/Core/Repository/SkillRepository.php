<?php

namespace App\Core\Repository;

use App\Core\Entity\Skill;
use App\Core\Util\Arrays;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Skill|null find($id, $lockMode = null, $lockVersion = null)
 * @method Skill|null findOneBy(array $criteria, array $orderBy = null)
 * @method Skill[]    findAll()
 * @method Skill[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SkillRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Skill::class);
    }

    public function findNames(): array
    {
        $qb = $this->createQueryBuilder('s')
            ->select('s.name')
            ->where('s.displayed = 1')
        ;

        return Arrays::flatten($qb->getQuery()
            ->getArrayResult());
    }

    public function findSome(int $length = 50): array
    {
        return $this->createQueryBuilder('s')
            ->orderBy('RAND()')
            ->setFirstResult(0)
            ->setMaxResults($length)
            ->getQuery()
            ->getResult()
        ;
    }

    public function countJobPostingsGroupBySkill(): array
    {
        return $this->createQueryBuilder('s')
            ->select('s as skill, count(s_jp) as count')
            ->leftJoin('s.jobPostings', 's_jp', Join::WITH, 's_jp.published = true')
            ->groupBy('s')
            ->getQuery()
            ->getResult()
        ;
    }

    public function searchDisplayed(string $search): array
    {
        return $this->createQueryBuilder('s')

            ->where('s.displayed = 1')
            ->andWhere('s.name LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->getQuery()
            ->getResult()
        ;
    }
}
