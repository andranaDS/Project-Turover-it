<?php

namespace App\Company\Repository;

use App\Company\Entity\CompanyUserFavorite;
use App\User\Contracts\UserInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CompanyUserFavorite|null find($id, $lockMode = null, $lockVersion = null)
 * @method CompanyUserFavorite|null findOneBy(array $criteria, array $orderBy = null)
 * @method CompanyUserFavorite[]    findAll()
 * @method CompanyUserFavorite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyUserFavoriteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CompanyUserFavorite::class);
    }

    public function findCompanyIdByUser(UserInterface $user): array
    {
        return $this->createQueryBuilder('cf')
            ->select('DISTINCT(cf.company) as companyId')
            ->where('cf.user = :user')
            ->setParameter('user', $user)
            ->orderBy('cf.company', Criteria::ASC)
            ->getQuery()
            ->getResult()
        ;
    }
}
