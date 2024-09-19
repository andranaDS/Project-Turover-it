<?php

namespace App\Company\Repository;

use App\Company\Entity\CompanyFeaturesUsage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CompanyFeaturesUsage|null find($id, $lockMode = null, $lockVersion = null)
 * @method CompanyFeaturesUsage|null findOneBy(array $criteria, array $orderBy = null)
 * @method CompanyFeaturesUsage[]    findAll()
 * @method CompanyFeaturesUsage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyFeaturesUsageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CompanyFeaturesUsage::class);
    }
}
