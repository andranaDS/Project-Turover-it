<?php

namespace App\User\Controller\FreeWork\User;

use App\User\Entity\User;
use App\User\Manager\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

final class Delete
{
    public function __invoke(User $data, Security $security, EntityManagerInterface $em, UserManager $um): Response
    {
        if ((null === $user = $security->getUser()) || !$user instanceof User) {
            return new Response(status: Response::HTTP_BAD_REQUEST);
        }

        $response = new Response(status: Response::HTTP_NO_CONTENT);

        // logout
        if ($user === $data) {
            $um->logout($response);
        }

        $um->deleteUser($data, $user);

        $em->flush();

        return $response;
    }
}
