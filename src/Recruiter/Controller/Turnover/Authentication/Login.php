<?php

namespace App\Recruiter\Controller\Turnover\Authentication;

use Symfony\Component\Routing\Annotation\Route;

final class Login
{
    /**
     * @Route(name="api_recruiter_turnover_authentication_login", path="/login", methods={"POST"}, host="%api_turnover_base_url%")
     */
    public function __invoke(): void
    {
    }
}
