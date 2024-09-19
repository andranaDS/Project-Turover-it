<?php

namespace App\Resource\Repository;

use App\Resource\Entity\TrendSkillLine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrendSkillLine|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrendSkillLine|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrendSkillLine[]    findAll()
 * @method TrendSkillLine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrendSkillLineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrendSkillLine::class);
    }
}
