<?php

namespace App\Core\Repository;

use App\Core\Entity\JobCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method JobCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method JobCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method JobCategory[]    findAll()
 * @method JobCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JobCategory::class);
    }
}
