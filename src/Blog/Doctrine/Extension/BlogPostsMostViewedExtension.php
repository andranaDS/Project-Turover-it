<?php

namespace App\Blog\Doctrine\Extension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Blog\Entity\BlogPostData;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class BlogPostsMostViewedExtension implements QueryCollectionExtensionInterface
{
    private ?Request $request;

    public function __construct(RequestStack $requestStack)
    {
        $this->request = $requestStack->getMainRequest();
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null): void
    {
        if ('freework_get_most_viewed' !== $operationName || null === $this->request) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder
            ->join(BlogPostData::class, 'bpd', Join::WITH, sprintf('%s.id = bpd.id', $rootAlias))
            ->andWhere(sprintf('%s.locales IS NULL OR JSON_CONTAINS(%s.locales, :locale) = 1', $rootAlias, $rootAlias))
            ->setParameter('locale', sprintf('"%s"', $this->request->getLocale()))
            ->orderBy('bpd.recentViewsCount', Criteria::DESC)
        ;
    }
}
