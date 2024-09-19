<?php

namespace App\User\Security\Guard;

use Lexik\Bundle\JWTAuthenticationBundle\Security\Authenticator\JWTAuthenticator;
use Symfony\Component\HttpFoundation\Request;

class JWTTokenAuthenticator extends JWTAuthenticator
{
    public function supports(Request $request): ?bool
    {
        if ('api_user_freework_security_logout' === $request->attributes->get('_route')) {
            return false;
        }

        return parent::supports($request);
    }
}
