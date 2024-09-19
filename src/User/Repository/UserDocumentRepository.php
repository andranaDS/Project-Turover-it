<?php

namespace App\User\Repository;

use App\User\Entity\UserDocument;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserDocument|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserDocument|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserDocument[]    findAll()
 * @method UserDocument[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserDocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserDocument::class);
    }

    public function findOldDefaultResumes(UserDocument $userDocument): array
    {
        return $this->createQueryBuilder('ud')
            ->select('ud')
            ->where('ud.user = :user')
            ->andWhere('ud.id != :userDocumentId')
            ->andWhere('ud.defaultResume = :true')
            ->setParameters([
                'user' => $userDocument->getUser(),
                'userDocumentId' => $userDocument->getId(),
                'true' => true,
            ])
            ->getQuery()
            ->getResult()
        ;
    }

    public function countUserResume(): int
    {
        return (int) $this->createQueryBuilder('ud')
            ->select('COUNT(DISTINCT ud.user)')
            ->where('ud.resume = true')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
