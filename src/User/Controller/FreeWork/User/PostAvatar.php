<?php

namespace App\User\Controller\FreeWork\User;

use App\User\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class PostAvatar
{
    public function __invoke(Request $request, User $data): User
    {
        $uploadedFile = $request->files->get('file');

        if (!$uploadedFile) {
            throw new BadRequestHttpException('"file" is required');
        }

        $data->setAvatarFile($uploadedFile);

        return $data;
    }
}
