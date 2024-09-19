<?php

namespace App\User\Repository;

use App\User\Entity\InsuranceCompany;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method InsuranceCompany|null find($id, $lockMode = null, $lockVersion = null)
 * @method InsuranceCompany|null findOneBy(array $criteria, array $orderBy = null)
 * @method InsuranceCompany[]    findAll()
 * @method InsuranceCompany[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InsuranceCompanyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InsuranceCompany::class);
    }
}
