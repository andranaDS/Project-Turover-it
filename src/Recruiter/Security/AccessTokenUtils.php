<?php

namespace App\Recruiter\Security;

use App\Core\Util\TokenGenerator;
use App\Recruiter\Entity\Recruiter;
use App\Recruiter\Entity\RecruiterAccessToken;
use Carbon\Carbon;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AccessTokenUtils
{
    public static string $cookieName = 'turnover_access_token';
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getAccessTokenValueFromCookie(Request $request): ?string
    {
        return $request->cookies->get(static::$cookieName);
    }

    public function getAccessTokenFromValue(string $value): ?RecruiterAccessToken
    {
        return $this->em->getRepository(RecruiterAccessToken::class)->findOneByValue(self::hashTokenValue($value));
    }

    public function createCookieFromAccessToken(RecruiterAccessToken $accessToken): Cookie
    {
        if (null === $accessToken->getExpiredAt()) {
            throw new \InvalidArgumentException();
        }

        return Cookie::create(static::$cookieName)
            ->withValue($accessToken->getPlainValue())
            ->withExpires($accessToken->getExpiredAt()->getTimestamp())
            ->withSecure()
            ->withHttpOnly()
        ;
    }

    public function createAccessToken(Recruiter $recruiter, int $ttl): RecruiterAccessToken
    {
        // delete old access tokens to disconnect devices
        $simultaneousConnectionsCount = 1;
        $oldAccessTokens = $this->em->getRepository(RecruiterAccessToken::class)->findBy(
            ['recruiter' => $recruiter],
            ['createdAt' => Criteria::DESC],
            null,
            $simultaneousConnectionsCount - 1
        );
        foreach ($oldAccessTokens as $oldAccessToken) {
            $this->em->remove($oldAccessToken);
        }

        // create new access token
        $newAccessToken = (new RecruiterAccessToken())
            ->setPlainValue(TokenGenerator::generate(32))
            ->setRecruiter($recruiter)
            ->setExpiredAt(Carbon::now()->addSeconds($ttl))
        ;
        $this->em->persist($newAccessToken);

        $this->em->flush();

        return $newAccessToken;
    }

    public function authenticateResponse(Recruiter $recruiter, int $ttl, Response $response): void
    {
        $accessToken = $this->createAccessToken($recruiter, $ttl);
        $cookie = $this->createCookieFromAccessToken($accessToken);
        $response->headers->setCookie($cookie);
    }

    public function logout(Recruiter $recruiter, Response $response): void
    {
        $this->logoutRecruiter($recruiter);
        $this->logoutResponse($response);
    }

    public function logoutRecruiter(Recruiter $recruiter): void
    {
        $accessTokens = $this->em->getRepository(RecruiterAccessToken::class)->findByRecruiter($recruiter);
        foreach ($accessTokens as $accessToken) {
            $this->em->remove($accessToken);
        }
        $this->em->flush();
    }

    public function logoutResponse(Response $response): void
    {
        $response->headers->clearCookie(self::$cookieName);
    }

    public static function hashTokenValue(string $value): string
    {
        return sha1($value);
    }
}
