<?php

namespace App\User\Repository;

use App\User\Entity\UmbrellaCompany;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UmbrellaCompany|null find($id, $lockMode = null, $lockVersion = null)
 * @method UmbrellaCompany|null findOneBy(array $criteria, array $orderBy = null)
 * @method UmbrellaCompany[]    findAll()
 * @method UmbrellaCompany[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UmbrellaCompanyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UmbrellaCompany::class);
    }
}
