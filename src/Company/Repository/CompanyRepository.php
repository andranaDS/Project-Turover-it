<?php

namespace App\Company\Repository;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;
use App\Company\Entity\Company;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Company|null find($id, $lockMode = null, $lockVersion = null)
 * @method Company|null findOneBy(array $criteria, array $orderBy = null)
 * @method Company[]    findAll()
 * @method Company[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Company::class);
    }

    public function exists(string $slug): bool
    {
        return null !== $this->createQueryBuilder('c')
                ->select('1')
                ->where('c.slug = :slug')
                ->setParameter('slug', $slug)
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult()
        ;
    }

    public function getHomepageCompanies(int $page = 1, int $itemsPerPage = 30): Paginator
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c', 'cp', 's', 'ba')
            ->addSelect('RAND() as HIDDEN rand')
            ->join('c.businessActivity', 'ba')
            ->join('c.pictures', 'cp')
            ->join('c.data', 'd')
            ->leftJoin('c.skills', 's')
            ->where('c.size IS NOT NULL')
            ->andWhere('c.businessActivity IS NOT NULL')
            ->andWhere('(c.location.latitude IS NOT NULL AND c.location.longitude IS NOT NULL)')
            ->andWhere('c.coverPicture IS NOT NULL')
            ->andWhere('d.jobPostingsPublishedCount > 0')
            ->andWhere("c.slug NOT LIKE 'agsi%' AND c.slug NOT LIKE 'free-work%' AND c.slug NOT LIKE 'freework%'")
            ->orderBy('rand')
        ;

        $query = $qb->getQuery()
            ->setFirstResult(($page - 1) * $itemsPerPage)
            ->setMaxResults($itemsPerPage)
        ;

        $doctrinePaginator = new DoctrinePaginator($query);

        return new Paginator($doctrinePaginator);
    }

    public function findSome(int $length = 50): array
    {
        return $this->createQueryBuilder('c')
            ->setFirstResult(0)
            ->setMaxResults($length)
            ->orderBy('c.id', Criteria::ASC)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findMatches(): array
    {
        $data = $this->createQueryBuilder('c')
            ->select('c.id, c.oldId')
            ->where('c.oldId IS NOT NULL')
            ->getQuery()
            ->getArrayResult()
        ;

        $matches = [];
        foreach ($data as $d) {
            $matches[$d['oldId']] = $d['id'];
        }

        return $matches;
    }
}
