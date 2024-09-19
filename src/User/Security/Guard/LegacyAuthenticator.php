<?php

namespace App\User\Security\Guard;

use App\Core\Util\Strings;
use App\User\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class LegacyAuthenticator extends AbstractAuthenticator
{
    private string $turnoverApiKey;

    public function __construct(string $turnoverItApiKey)
    {
        $this->turnoverApiKey = $turnoverItApiKey;
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has('X-AUTH-TOKEN') && Strings::contains($request->attributes->get('_route'), '_legacy_');
    }

    public function authenticate(Request $request): SelfValidatingPassport
    {
        $token = $request->headers->get('X-AUTH-TOKEN');

        if (null === $token) {
            throw new CustomUserMessageAuthenticationException('No API token provided');
        }

        if ($this->turnoverApiKey !== $token) {
            throw new CustomUserMessageAuthenticationException('Invalid token provided');
        }

        return new SelfValidatingPassport(new UserBadge($token, function () {
            return (new User())
                ->setEnabled(true)
                ->setLocked(false)
                ->addRole('ROLE_LEGACY')
            ;
        }));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData()),
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}
