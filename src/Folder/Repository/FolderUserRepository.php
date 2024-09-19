<?php

namespace App\Folder\Repository;

use App\Folder\Entity\Folder;
use App\Folder\Entity\FolderUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

class FolderUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FolderUser::class);
    }

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countByFolder(Folder $folder): int
    {
        return $this->createQueryBuilder('fu')
            ->select('COUNT(fu)')
            ->where('fu.folder = :folder')
            ->setParameter('folder', $folder)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
