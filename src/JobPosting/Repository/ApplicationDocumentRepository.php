<?php

namespace App\JobPosting\Repository;

use App\JobPosting\Entity\ApplicationDocument;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ApplicationDocument|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApplicationDocument|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApplicationDocument[]    findAll()
 * @method ApplicationDocument[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApplicationDocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApplicationDocument::class);
    }
}
