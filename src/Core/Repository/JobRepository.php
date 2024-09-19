<?php

namespace App\Core\Repository;

use App\Core\Entity\Job;
use App\Core\Util\Arrays;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Job|null find($id, $lockMode = null, $lockVersion = null)
 * @method Job|null findOneBy(array $criteria, array $orderBy = null)
 * @method Job[]    findAll()
 * @method Job[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Job::class);
    }

    public function findNames(): array
    {
        return Arrays::flatten($this->createQueryBuilder('j')
            ->select('j.name')
            ->getQuery()
            ->getArrayResult());
    }

    public function findAllAsIterable(): iterable
    {
        return $this->createQueryBuilder('j')
            ->getQuery()
            ->toIterable()
        ;
    }

    public function searchNameForContributionS(string $search): array
    {
        return $this->createQueryBuilder('j')
            ->where('j.availableForContribution = 1')
            ->andWhere('j.nameForContribution LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->getQuery()
            ->getResult()
        ;
    }
}
