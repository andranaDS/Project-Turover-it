<?php

namespace App\User\Controller\FreeWork\User;

use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Vich\UploaderBundle\Handler\UploadHandler;

final class DeleteAvatar
{
    public function __invoke(EntityManagerInterface $em, UploadHandler $uploadHandler, Request $request, User $data): Response
    {
        $uploadHandler->remove($data, 'avatarFile');
        $data->setAvatar(null);
        $data->setAvatarFile(null);

        $em->flush();

        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
