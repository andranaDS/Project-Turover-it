<?php

namespace App\Core\Repository;

use App\Core\Entity\LocationKeyLabel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LocationKeyLabel|null find($id, $lockMode = null, $lockVersion = null)
 * @method LocationKeyLabel|null findOneBy(array $criteria, array $orderBy = null)
 * @method LocationKeyLabel[]    findAll()
 * @method LocationKeyLabel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LocationKeyLabelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LocationKeyLabel::class);
    }
}
