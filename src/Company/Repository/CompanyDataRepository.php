<?php

namespace App\Company\Repository;

use App\Company\Entity\CompanyData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CompanyData|null find($id, $lockMode = null, $lockVersion = null)
 * @method CompanyData|null findOneBy(array $criteria, array $orderBy = null)
 * @method CompanyData[]    findAll()
 * @method CompanyData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CompanyData::class);
    }
}
