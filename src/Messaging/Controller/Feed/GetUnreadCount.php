<?php

namespace App\Messaging\Controller\Feed;

use App\Messaging\Repository\FeedRepository;
use App\User\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

class GetUnreadCount
{
    public function __invoke(Security $security, FeedRepository $feedRepository): JsonResponse
    {
        if ((null === $user = $security->getUser()) || !$user instanceof User) {
            return new JsonResponse(status: Response::HTTP_BAD_REQUEST);
        }

        $count = $feedRepository->countUnread($user);

        return new JsonResponse($count);
    }
}
