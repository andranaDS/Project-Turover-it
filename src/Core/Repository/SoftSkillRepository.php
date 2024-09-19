<?php

namespace App\Core\Repository;

use App\Core\Entity\SoftSkill;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SoftSkill|null find($id, $lockMode = null, $lockVersion = null)
 * @method SoftSkill|null findOneBy(array $criteria, array $orderBy = null)
 * @method SoftSkill[]    findAll()
 * @method SoftSkill[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SoftSkillRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SoftSkill::class);
    }
}
