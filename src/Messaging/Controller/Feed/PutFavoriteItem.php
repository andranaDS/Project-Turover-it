<?php

namespace App\Messaging\Controller\Feed;

use App\Messaging\Entity\Feed;
use App\Messaging\Entity\FeedUser;
use App\User\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Security;

class PutFavoriteItem
{
    public function __invoke(Request $request, Feed $data, Security $security): Feed
    {
        /** @var ?User $user */
        $user = $security->getUser();

        if (null === $user || !$data->hasUser($user)) {
            throw new UnauthorizedHttpException('unauthorized');
        }

        $feedUser = $data->getFeedUser($user);

        if ($feedUser instanceof FeedUser) {
            $feedUser->setFavorite(!$feedUser->getFavorite());
        }

        return $data;
    }
}
