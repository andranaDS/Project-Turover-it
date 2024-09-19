<?php

namespace App\User\EventSubscriber;

use Gesdinet\JWTRefreshTokenBundle\Service\RefreshToken;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationSuccessResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\PreAuthenticationJWTUserToken;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\TokenExtractorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class JWTAutoRefreshSubscriber implements EventSubscriberInterface
{
    private TokenExtractorInterface $tokenExtractor;
    private JWTTokenManagerInterface $jwtManager;
    private RefreshToken $refreshToken;
    private array $cookies;
    private string $jwtHpName;
    private string $jwtSName;

    public function __construct(TokenExtractorInterface $tokenExtractor, JWTTokenManagerInterface $jwtManager, string $jwtHpName, string $jwtSName, RefreshToken $refreshToken)
    {
        $this->tokenExtractor = $tokenExtractor;
        $this->jwtManager = $jwtManager;
        $this->refreshToken = $refreshToken;
        $this->cookies = [];
        $this->jwtHpName = $jwtHpName;
        $this->jwtSName = $jwtSName;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 10],
            KernelEvents::RESPONSE => ['onKernelResponse', -10000],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (\in_array($request->get('_route'), ['api_user_freework_security_login', 'api_user_freework_security_logout'], true)) {
            // there is no need to refresh the token on login and logout routes
            return;
        }

        $jwt = $this->tokenExtractor->extract($request);
        $jwtValid = false;

        // if the token exists, check the token validity
        if (false !== $jwt) {
            try {
                $preAuthToken = new PreAuthenticationJWTUserToken($jwt);
                $jwtValid = false === $this->jwtManager->decode($preAuthToken) ? false : true;
            } catch (JWTDecodeFailureException $e) {
                $jwtValid = false;
            }
        }

        // token is valid
        if (true === $jwtValid) {
            return;
        }

        // token is invalid : try to refresh jwt token with refresh token cookie
        $refreshTokenResponse = $this->refreshToken->refresh($request);
        if (!$refreshTokenResponse instanceof JWTAuthenticationSuccessResponse) {
            return;
        }

        // get jwt cookies from refresh token response
        $this->cookies = [];
        foreach ($refreshTokenResponse->headers->getCookies() as $cookie) {
            /* @var Cookie */
            if (\in_array($cookie->getName(), [$this->jwtHpName, $this->jwtSName], true)) {
                $this->cookies[$cookie->getName()] = $cookie;
            }
        }

        if (empty($this->cookies)) {
            return;
        }

        // override cookies values for the current request
        $request->cookies->replace(array_map(static function (Cookie $cookie) {
            return $cookie->getValue();
        }, $this->cookies));
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (empty($this->cookies)) {
            return;
        }

        $response = $event->getResponse();

        // add jwt cookies to the response
        foreach ($this->cookies as $cookie) {
            /** @var Cookie $cookie */
            if (\in_array($cookie->getName(), [$this->jwtHpName, $this->jwtSName], true)) {
                $response->headers->setCookie($cookie);
            }
        }
    }
}
