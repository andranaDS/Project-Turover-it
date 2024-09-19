<?php

namespace App\User\Controller\FreeWork\Security;

use Symfony\Component\Routing\Annotation\Route;

final class LoginProvider
{
    /**
     * @Route(
     *     name="api_user_freework_security_login_provider",
     *     path="/login/callback/{provider}",
     *     methods={"GET"},
     *     condition= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'"
     * )
     */
    public function __invoke(): void
    {
    }
}
