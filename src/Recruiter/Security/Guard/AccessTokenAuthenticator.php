<?php

namespace App\Recruiter\Security\Guard;

use App\Recruiter\Security\AccessTokenUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class AccessTokenAuthenticator extends AbstractAuthenticator
{
    private AccessTokenUtils $atu;

    public function __construct(AccessTokenUtils $atu)
    {
        $this->atu = $atu;
    }

    public function supports(Request $request): ?bool
    {
        return null !== $this->atu->getAccessTokenValueFromCookie($request);
    }

    public function authenticate(Request $request): Passport
    {
        if (null === $accessTokenValue = $this->atu->getAccessTokenValueFromCookie($request)) {
            throw new AuthenticationCredentialsNotFoundException();
        }

        if (null === $accessToken = $this->atu->getAccessTokenFromValue($accessTokenValue)) {
            throw new BadCredentialsException();
        }

        if (null === $recruiter = $accessToken->getRecruiter()) {
            throw new BadCredentialsException();
        }

        return new SelfValidatingPassport(new UserBadge('recruiter-' . $recruiter->getId(), function () use ($recruiter) {
            return $recruiter;
        }));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return null;
    }
}
