<?php

namespace App\Partner\Repository;

use App\Partner\Entity\Partner;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Partner|null find($id, $lockMode = null, $lockVersion = null)
 * @method Partner|null findOneBy(array $criteria, array $orderBy = null)
 * @method Partner[]    findAll()
 * @method Partner[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PartnerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Partner::class);
    }

    public function getDistributionsRange(): iterable
    {
        $partners = $this->createQueryBuilder('p')
            ->select('p')
            ->getQuery()
            ->getResult()
        ;

        $rangeEnd = 0;
        foreach ($partners as $partner) {
            /* @var Partner $partner */
            yield ['partner' => $partner, 'range' => $rangeEnd + $partner->getDistribution()];
            $rangeEnd += $partner->getDistribution();
        }
    }
}
