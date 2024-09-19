<?php

namespace App\Recruiter\Security;

use App\Recruiter\Entity\Recruiter;
use Nette\Utils\Json;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private int $accessTokenTtl;
    private int $accessTokenTtlRemember;
    private AccessTokenUtils $accessTokenUtils;

    public function __construct(AccessTokenUtils $acu, int $accessTokenTtl, int $accessTokenTtlRemember)
    {
        $this->accessTokenTtl = $accessTokenTtl;
        $this->accessTokenTtlRemember = $accessTokenTtlRemember;
        $this->accessTokenUtils = $acu;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): Response
    {
        $recruiter = $token->getUser();
        if (!$recruiter instanceof Recruiter) {
            throw new \InvalidArgumentException();
        }

        $remember = Json::decode($request->getContent(), Json::FORCE_ARRAY)['remember'] ?? false;
        $ttl = true === $remember ? $this->accessTokenTtlRemember : $this->accessTokenTtl;

        $response = new Response(status: Response::HTTP_NO_CONTENT);
        $this->accessTokenUtils->authenticateResponse($recruiter, $ttl, $response);

        return $response;
    }
}
