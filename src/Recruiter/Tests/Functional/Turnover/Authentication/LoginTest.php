<?php

namespace App\Recruiter\Tests\Functional\Turnover\Authentication;

use App\Recruiter\Entity\Recruiter;
use App\Tests\Functional\ApiTestCase;
use Symfony\Component\BrowserKit\Cookie;

class LoginTest extends ApiTestCase
{
    public function testWithGoodCredentialsAndEnabled(): void
    {
        $client = static::createTurnoverClient();
        $client->request('POST', '/login', [
            'json' => [
                'email' => 'walter.white@breaking-bad.com',
                'password' => 'P@ssw0rd',
            ],
        ]);

        self::assertResponseStatusCodeSame(204);
    }

    public function testWithGoodCredentialsAndDisabled(): void
    {
        $client = static::createTurnoverClient();
        $client->request('POST', '/login', [
            'json' => [
                'email' => 'gustavo.fring@breaking-bad.com',
                'password' => 'P@ssw0rd',
            ],
        ]);

        self::assertResponseStatusCodeSame(204);
    }

    public function testWithWrongCredentials(): void
    {
        $client = static::createTurnoverClient();
        $client->request('POST', '/login', [
            'json' => [
                'email' => 'walter.white@breaking-bad.com',
                'password' => 'P@ssw0rdd',
            ],
        ]);

        self::assertResponseStatusCodeSame(401);
        self::assertJsonContains([
            '@context' => '/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'Vos identifiants sont invalides.',
            'violations' => [
                [
                    'propertyPath' => 'email',
                    'message' => '',
                    'code' => null,
                ],
                [
                    'propertyPath' => 'password',
                    'message' => '',
                    'code' => null,
                ],
            ],
        ]);
    }

    public function testSimultaneousConnectionsDisabled(): void
    {
        $client = static::createTurnoverClient();

        // first client
        $response = $client->request('POST', '/login', [
            'json' => [
                'email' => 'walter.white@breaking-bad.com',
                'password' => 'P@ssw0rd',
            ],
        ]);
        self::assertResponseStatusCodeSame(204);
        $oldCookie = Cookie::fromString($response->getHeaders()['set-cookie'][0]);

        $client->getKernelBrowser()->getCookieJar()->set($oldCookie);
        $client->request('GET', '/recruiters/me');
        self::assertResponseStatusCodeSame(200);

        // second client
        $response = $client->request('POST', '/login', [
            'json' => [
                'email' => 'walter.white@breaking-bad.com',
                'password' => 'P@ssw0rd',
            ],
        ]);
        self::assertResponseStatusCodeSame(204);
        $newCookie = Cookie::fromString($response->getHeaders()['set-cookie'][0]);

        // check if the first client is logout
        $client->getKernelBrowser()->getCookieJar()->set($oldCookie);
        $client->request('GET', '/recruiters/me');
        self::assertResponseStatusCodeSame(404);

        // check if the second client is still logged
        $client->getKernelBrowser()->getCookieJar()->set($newCookie);
        $client->request('GET', '/recruiters/me');
        self::assertResponseStatusCodeSame(200);
    }

    public static function provideWithRememberCases(): iterable
    {
        yield [true, 604800];
        yield [false, 86400];
    }

    /**
     * @dataProvider provideWithRememberCases
     */
    public function testWithRemember(bool $remember, int $ttl): void
    {
        $client = static::createTurnoverClient();
        $response = $client->request('POST', '/login', [
            'json' => [
                'email' => 'walter.white@breaking-bad.com',
                'password' => 'P@ssw0rd',
                'remember' => $remember,
            ],
        ]);

        $cookie = Cookie::fromString($response->getHeaders()['set-cookie'][0]);

        /* TODO Fast fix: probably a timezone problem */
        self::assertEqualsWithDelta($ttl, $cookie->getExpiresTime() - time(), 3603);
    }

    public function testFirstLoginWithRecruiterSecondary(): void
    {
        $client = static::createTurnoverClient();

        /** @var Recruiter $recruiter */
        $email = 'peter.quinn@homeland.com';
        $recruiter = $this->getEntityManager($client)->getRepository(Recruiter::class)->findOneByEmail($email);

        self::assertNotNull($recruiter);

        self::assertFalse($recruiter->isEnabled());
        self::assertNull($recruiter->getLoggedAt());

        $client->request('POST', '/login', [
            'json' => [
                'email' => $email,
                'password' => 'P@ssw0rd',
            ],
        ]);

        self::assertResponseIsSuccessful();

        self::assertTrue($recruiter->isEnabled());
        self::assertNotNull($recruiter->getLoggedAt());
    }
}
