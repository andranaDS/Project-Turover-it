<?php

namespace App\Messaging\Controller\Feed;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;
use App\Messaging\Repository\FeedRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class GetCollectionOrderFavorite
{
    public function __invoke(Request $request, FeedRepository $feedRepository, Security $security, int $itemsPerPageDefault): Paginator
    {
        $page = (int) $request->query->get('page', '1');
        $itemsPerPage = (int) $request->query->get('itemsPerPage', (string) $itemsPerPageDefault);
        $q = (string) $request->query->get('q', null);

        return $feedRepository->findUserFeedsOrderFavorite($security->getUser(), $page, $itemsPerPage, $q);
    }
}
