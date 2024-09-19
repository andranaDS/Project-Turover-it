<?php

namespace App\Messaging\Doctrine\Extension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Messaging\Entity\Feed;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

class FeedExtension implements QueryCollectionExtensionInterface
{
    private RequestStack $requestStack;
    private Security $security;

    public function __construct(RequestStack $requestStack, Security $security)
    {
        $this->requestStack = $requestStack;
        $this->security = $security;
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        if (Feed::class !== $resourceClass) {
            return;
        }

        if ((null === $request = $this->requestStack->getMainRequest()) || (null === $user = $this->security->getUser())) {
            return;
        }

        if ('api_feeds_get_collection' !== $request->attributes->get('_route')) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->join(sprintf('%s.feedUsers', $rootAlias), 'fu')
            ->andWhere('fu.user = :user')
            ->setParameter('user', $user)
        ;
    }
}
