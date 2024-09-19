<?php

namespace App\User\Controller\FreeWork\Security;

use App\User\Manager\UserManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class Logout
{
    /**
     * @Route(
     *     name="api_user_freework_security_logout",
     *     path="/logout",
     *     methods={"GET"},
     *     condition= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
     * )
     */
    public function __invoke(UserManager $um): Response
    {
        $response = new Response();

        $um->logout($response);

        return $response;
    }
}
