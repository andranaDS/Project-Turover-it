<?php

namespace App\Core\Repository;

use App\Core\Entity\ForbiddenContent;
use App\Core\Util\Arrays;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ForbiddenContent|null find($id, $lockMode = null, $lockVersion = null)
 * @method ForbiddenContent|null findOneBy(array $criteria, array $orderBy = null)
 * @method ForbiddenContent[]    findAll()
 * @method ForbiddenContent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ForbiddenContentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ForbiddenContent::class);
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
