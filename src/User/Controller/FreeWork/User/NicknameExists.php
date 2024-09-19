<?php

namespace App\User\Controller\FreeWork\User;

use App\User\Entity\User;
use App\User\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;

final class NicknameExists
{
    public function __invoke(UserRepository $userRepository, ?string $nickname): Response
    {
        if (null === $nickname) {
            return new Response(status: Response::HTTP_BAD_REQUEST);
        }

        $user = $userRepository->findOneBy([
            'nickname' => $nickname,
        ]);

        if ($user instanceof User) {
            return new Response(status: Response::HTTP_OK);
        }

        return new Response(status: Response::HTTP_NOT_FOUND);
    }
}
