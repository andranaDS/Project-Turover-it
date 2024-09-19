<?php

namespace App\Recruiter\Repository;

use App\Recruiter\Entity\RecruiterJob;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<RecruiterJob>
 *
 * @method RecruiterJob|null find($id, $lockMode = null, $lockVersion = null)
 * @method RecruiterJob|null findOneBy(array $criteria, array $orderBy = null)
 * @method RecruiterJob[]    findAll()
 * @method RecruiterJob[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecruiterJobRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RecruiterJob::class);
    }

    public function add(RecruiterJob $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(RecruiterJob $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
