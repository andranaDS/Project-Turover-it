<?php

namespace App\User\Doctrine\Extension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Recruiter\Entity\Recruiter;
use App\User\Entity\User;
use App\User\Entity\UserTrace;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

class UsersLastViewedExtension implements QueryCollectionExtensionInterface
{
    public const NUMBER_DAY = 30;

    public function __construct(private RequestStack $requestStack, private Security $security)
    {
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null): void
    {
        if (null === $request = $this->requestStack->getCurrentRequest()) {
            return;
        }

        if (User::class === $resourceClass && 'api_users_turnover_get_last_viewed_collection' === $request->attributes->get('_route')) {
            if (!$this->security->isGranted('ROLE_RECRUITER')) {
                throw new \LogicException('Acces denied');
            }

            /** @var Recruiter $recruiter */
            $recruiter = $this->security->getUser();
            $this->andWhere($queryBuilder, $recruiter);
        }
    }

    public function andWhere(QueryBuilder $queryBuilder, Recruiter $recruiter): void
    {
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $dayAgo = (new \DateTime())->modify('- ' . self::NUMBER_DAY . ' days')->format('Y-m-d');

        $queryBuilder->leftJoin(UserTrace::class, 'ut', Join::WITH, sprintf('ut.user = %s.id', $rootAlias))
                     ->andWhere('ut.recruiter = :recruiter')
                     ->andWhere('CURRENT_DATE() >= ut.viewedAt')
                     ->andWhere('ut.viewedAt >= :dayAgo')
                     ->setParameter('recruiter', $recruiter)
                     ->setParameter('dayAgo', $dayAgo)
                     ->orderBy(sprintf('%s.id', $rootAlias), 'DESC')
        ;
    }
}
