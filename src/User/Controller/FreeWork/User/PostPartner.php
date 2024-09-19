<?php

namespace App\User\Controller\FreeWork\User;

use App\User\Entity\User;
use Nette\Utils\Json;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class PostPartner
{
    public function __invoke(Request $request, User $data): User
    {
        $requestData = Json::decode($request->getContent(), Json::FORCE_ARRAY);

        if (false === isset($requestData['partner'])) {
            throw new BadRequestHttpException('"partner" is required');
        }

        if (null !== $data->getPartner()) {
            throw new AccessDeniedException();
        }

        $data->setPartner($requestData['partner']);

        return $data;
    }
}
