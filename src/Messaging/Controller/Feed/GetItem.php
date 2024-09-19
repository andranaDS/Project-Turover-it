<?php

namespace App\Messaging\Controller\Feed;

use App\Messaging\Entity\Feed;
use App\User\Entity\User;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Security;

class GetItem
{
    public function __invoke(Feed $data, Security $security): Feed
    {
        /** @var ?User $user */
        $user = $security->getUser();

        if (null === $user || !$data->hasUser($user)) {
            throw new UnauthorizedHttpException('unauthorized');
        }

        return $data;
    }
}
