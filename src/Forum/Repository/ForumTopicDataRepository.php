<?php

namespace App\Forum\Repository;

use App\Forum\Entity\ForumCategory;
use App\Forum\Entity\ForumTopic;
use App\Forum\Entity\ForumTopicData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ForumTopicData|null find($id, $lockMode = null, $lockVersion = null)
 * @method ForumTopicData|null findOneBy(array $criteria, array $orderBy = null)
 * @method ForumTopicData[]    findAll()
 * @method ForumTopicData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ForumTopicDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ForumTopicData::class);
    }

    public function getViewsCount(?ForumCategory $category = null): array
    {
        $qb = $this->createQueryBuilder('ftd')
            ->select('ftd.id, ftd.viewsCount')
            ->leftJoin(ForumTopic::class, 'ft', Join::WITH, 'ft.id = ftd.id')
        ;

        if (null !== $category) {
            $qb->where('ft.category = :category')
                ->setParameter('category', $category)
            ;
        }

        return $qb->getQuery()
            ->getArrayResult()
        ;
    }
}
