<?php

namespace App\Folder\Repository;

use App\Folder\Entity\Folder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

class FolderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Folder::class);
    }

    public function findByTypes(array $types): iterable
    {
        if (empty($types)) {
            return [];
        }

        return $this->createQueryBuilder('f')
            ->where('f.type IN (:type)')
            ->setParameter('type', $types)
            ->orderBy('f.recruiter', Criteria::ASC)
            ->getQuery()
            ->toIterable()
        ;
    }
}
