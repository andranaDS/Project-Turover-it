<?php

namespace App\User\Controller\FreeWork\User;

use App\User\Entity\User;
use App\User\Manager\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DeleteProfile
{
    public function __invoke(EntityManagerInterface $em, Request $request, UserManager $um, User $data): Response
    {
        $um->deleteProfile($data);

        $em->flush();

        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
