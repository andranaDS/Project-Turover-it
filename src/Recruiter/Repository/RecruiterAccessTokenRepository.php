<?php

namespace App\Recruiter\Repository;

use App\Recruiter\Entity\Recruiter;
use App\Recruiter\Entity\RecruiterAccessToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<Recruiter>
 *
 * @method Recruiter|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recruiter|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recruiter[]    findAll()
 * @method Recruiter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecruiterAccessTokenRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RecruiterAccessToken::class);
    }

    public function add(RecruiterAccessToken $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(RecruiterAccessToken $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOneByValue(string $value): ?RecruiterAccessToken
    {
        return $this->createQueryBuilder('rac')
            ->select('rac, rac_r')
            ->join('rac.recruiter', 'rac_r')
            ->where('rac.value = :value')
            ->setParameter('value', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOneByRecruiterEmail(string $email): ?RecruiterAccessToken
    {
        return $this->createQueryBuilder('rac')
            ->select('rac, rac_r')
            ->join('rac.recruiter', 'rac_r')
            ->where('rac_r.email = :email')
            ->setParameter('email', $email)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
