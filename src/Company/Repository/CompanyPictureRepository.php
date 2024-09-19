<?php

namespace App\Company\Repository;

use App\Company\Entity\CompanyPicture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CompanyPicture|null find($id, $lockMode = null, $lockVersion = null)
 * @method CompanyPicture|null findOneBy(array $criteria, array $orderBy = null)
 * @method CompanyPicture[]    findAll()
 * @method CompanyPicture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyPictureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CompanyPicture::class);
    }
}
