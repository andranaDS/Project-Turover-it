<?php

namespace App\Core\Repository;

use App\Core\Entity\SensitiveContent;
use App\Core\Util\Arrays;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SensitiveContent|null find($id, $lockMode = null, $lockVersion = null)
 * @method SensitiveContent|null findOneBy(array $criteria, array $orderBy = null)
 * @method SensitiveContent[]    findAll()
 * @method SensitiveContent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SensitiveContentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SensitiveContent::class);
    }

    public function findContents(): array
    {
        $result = $this->createQueryBuilder('w')
            ->select('LOWER(w.text) as text')
            ->getQuery()
            ->getArrayResult()
        ;

        return Arrays::map($result, function (array $r) {
            return $r['text'];
        });
    }
}
