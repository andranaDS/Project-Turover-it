<?php

namespace App\Company\Repository;

use App\Company\Entity\CompanyRecruiterFavorite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CompanyRecruiterFavorite|null find($id, $lockMode = null, $lockVersion = null)
 * @method CompanyRecruiterFavorite|null findOneBy(array $criteria, array $orderBy = null)
 * @method CompanyRecruiterFavorite[]    findAll()
 * @method CompanyRecruiterFavorite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyRecruiterFavoriteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CompanyRecruiterFavorite::class);
    }
}
