<?php

namespace App\User\Repository;

use App\User\Entity\UserProvider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserProvider|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserProvider|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserProvider[]    findAll()
 * @method UserProvider[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserProviderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserProvider::class);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneByProvider(string $provider, string $providerUserId): ?UserProvider
    {
        return $this->createQueryBuilder('uoc')
            ->join('uoc.user', 'u')
            ->addSelect('u')
            ->where('uoc.provider = :provider')
            ->andWhere('uoc.providerUserId = :providerUserId')
            ->setParameters([
                'provider' => $provider,
                'providerUserId' => $providerUserId,
            ])
            ->setFirstResult(0)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
