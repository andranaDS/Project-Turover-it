<?php

namespace App\User\Controller\FreeWork\Playwright;

use App\User\Entity\User;
use App\User\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ConfirmationToken
{
    /**
     * @Route(
     *     name="api_user_freework_playwright_confirmation_token",
     *     path="/playwright/user/{id}/confirmation-token",
     *     methods={"GET"},
     *     condition=" '%cluster%' != 'prod' and request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
     *     )
     */
    public function __invoke(int $id, UserRepository $userRepository): JsonResponse
    {
        if (null === $user = $userRepository->findOneById($id)) {
            throw new NotFoundHttpException();
        }

        /* @var User $user */
        return new JsonResponse(
            [
                'confirmationToken' => $user->getConfirmationToken(),
            ]
        );
    }
}
