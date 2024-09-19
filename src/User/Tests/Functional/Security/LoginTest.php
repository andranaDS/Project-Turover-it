<?php

namespace App\User\Tests\Functional\Security;

use App\Tests\Functional\ApiTestCase;
use App\User\Entity\User;

use function Symfony\Component\String\u;

class LoginTest extends ApiTestCase
{
    public function testWithValidData(): void
    {
        $client = static::createFreeWorkClient();
        $email = 'user@free-work.fr';

        $container = $client->getContainer();
        if (null === $container) {
            throw new \RuntimeException('Container is null');
        }
        $em = $container->get('doctrine')->getManager();
        $user = $em->getRepository(User::class)->findOneBy([
            'email' => $email,
        ]);
        self::assertNotNull($user);
        self::assertNull($user->getLastLoginAt());
        self::assertNull($user->getLastLoginProvider());

        $response = $client->request('POST', '/login', [
            'json' => [
                'email' => $email,
                'password' => 'P@ssw0rd',
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertNotNull($user->getLastLoginAt());
        self::assertNotNull($user->getLastLoginProvider());
        self::assertSame('email', $user->getLastLoginProvider());
        self::assertSame((new \DateTime())->format('d-m-Y'), $user->getLastLoginAt()->format('d-m-Y'));

        $cookies = $response->getHeaders()['set-cookie'] ?? null;
        self::assertNotNull($cookies, 'Cookies not found');

        $cookiesCount = \count($cookies);
        self::assertSame(3, $cookiesCount);

        $cookiesToCheck = [
            [
                'name' => 'jwt_s',
                'regex' => '[A-Za-z0-9-_.+/=]*',
            ],
            [
                'name' => 'jwt_hp',
                'regex' => '[A-Za-z0-9-_=]+\.[A-Za-z0-9-_=]+',
            ],
            [
                'name' => 'refresh_token',
                'regex' => '[a-z0-9]+',
            ],
        ];

        foreach ($cookiesToCheck as $cookieToCheck) {
            $cookieToCheckFound = array_filter($cookies, function (string $cookie) use ($cookieToCheck) {
                return u($cookie)->startsWith($cookieToCheck['name'] . '=');
            });
            $cookieToCheckFound = array_shift($cookieToCheckFound);

            self::assertNotNull($cookieToCheckFound, sprintf('Cookie "%s" not found', $cookieToCheck['name']));
            self::assertMatchesRegularExpression(sprintf('~^%s=%s;.*$~', $cookieToCheck['name'], $cookieToCheck['regex']), $cookieToCheckFound, sprintf('Cookie "%s" not valid', $cookieToCheck['name']));
        }
    }

    public function testLoginWithInvalidData(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('POST', '/login', [
            'json' => [
                'email' => 'user@free-work.fr',
                'password' => 'wrong_password',
            ],
        ]);

        self::assertResponseStatusCodeSame(401);
        self::assertResponseHeaderSame('content-type', 'application/json');
        self::assertJsonContains([
            '@context' => '/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'Vos identifiants sont invalides.',
            'violations' => [
                [
                    'propertyPath' => 'email',
                    'message' => '',
                ],
                [
                    'propertyPath' => 'password',
                    'message' => '',
                ],
            ],
        ]);
    }

    public function testLoginWithEmptyData(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('POST', '/login', [
            'json' => [
                'email' => '',
                'password' => '',
            ],
        ]);

        self::assertResponseStatusCodeSame(401);
        self::assertResponseHeaderSame('content-type', 'application/json');
        self::assertJsonContains([
            '@context' => '/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'Vos identifiants sont invalides.',
            'violations' => [
                [
                    'propertyPath' => 'email',
                    'message' => '',
                ],
                [
                    'propertyPath' => 'password',
                    'message' => '',
                ],
            ],
        ]);
    }

    public function testWithRememberMe(): void
    {
        $client = static::createFreeWorkClient();
        $response = $client->request('POST', '/login', [
            'json' => [
                'email' => 'user@free-work.fr',
                'password' => 'P@ssw0rd',
                'remember' => true,
            ],
        ]);

        self::assertResponseIsSuccessful();

        $cookies = self::extractCookies($response);
        $cookiesCount = \count($cookies);

        self::assertSame(3, $cookiesCount);
        self::assertEqualsWithDelta(3600, (int) $cookies[0], 2);
        self::assertEqualsWithDelta(3600, (int) $cookies[1], 2);
        self::assertEqualsWithDelta(604800, (int) $cookies[2], 2);
    }

    public function testWithoutRememberMe(): void
    {
        $client = static::createFreeWorkClient();
        $response = $client->request('POST', '/login', [
            'json' => [
                'email' => 'user@free-work.fr',
                'password' => 'P@ssw0rd',
                'remember' => false,
            ],
        ]);

        self::assertResponseIsSuccessful();

        $cookies = $response->getHeaders()['set-cookie'] ?? [];
        sort($cookies);

        $cookies = array_map(static function (string $cookie) {
            $parts = array_map('trim', explode(';', $cookie));
            sort($parts);

            return substr($parts[0], 8);
        }, $cookies);
        $cookiesCount = \count($cookies);

        self::assertSame(3, $cookiesCount);
        self::assertEqualsWithDelta(3600, (int) $cookies[0], 2);
        self::assertEqualsWithDelta(3600, (int) $cookies[1], 2);
        self::assertEqualsWithDelta(86400, (int) $cookies[2], 2);
    }

    public function testLocked(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('POST', '/login', [
            'json' => [
                'email' => 'user-locked@free-work.fr',
                'password' => 'P@ssw0rd',
            ],
        ]);

        self::assertResponseStatusCodeSame(401);
        self::assertResponseHeaderSame('content-type', 'application/json');
        self::assertJsonContains([
            '@context' => '/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'Locked.',
            'violations' => [
                [
                    'propertyPath' => 'email',
                    'message' => 'Votre compte est verrouillé.',
                ],
            ],
        ]);
    }

    public function testDisabled(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('POST', '/login', [
            'json' => [
                'email' => 'user-not-enabled@free-work.fr',
                'password' => 'P@ssw0rd',
            ],
        ]);

        self::assertResponseStatusCodeSame(401);
        self::assertResponseHeaderSame('content-type', 'application/json');
        self::assertJsonContains([
            '@context' => '/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'Disabled.',
            'violations' => [
                [
                    'propertyPath' => 'email',
                    'message' => 'L\'email de votre compte n\'a pas été confirmé.',
                ],
            ],
        ]);
    }

    public function testLoginFromOldFreelanceInfoUser(): void
    {
        $client = static::createFreeWorkClient();
        $email = 'user-from-freelance-info@free-work.fr';

        $container = $client->getContainer();
        if (null === $container) {
            throw new \RuntimeException('Container is null');
        }
        $em = $container->get('doctrine')->getManager();
        $user = $em->getRepository(User::class)->findOneBy([
            'email' => $email,
        ]);
        self::assertNotNull($user);
        $oldPassword = $user->getPassword();
        self::assertNotNull($oldPassword);
        self::assertStringNotContainsString('argon', $oldPassword);

        $client->request('POST', '/login', [
            'json' => [
                'email' => $email,
                'password' => 'P@ssw0rd',
            ],
        ]);

        self::assertResponseIsSuccessful();

        $user = $em->getRepository(User::class)->findOneBy([
            'email' => $email,
        ]);
        self::assertNotNull($user);
        $newPassword = $user->getPassword();
        self::assertNotNull($newPassword);
        self::assertSame($oldPassword, $newPassword);
    }

    public function testLoginFromOldCarriereInfoUser(): void
    {
        $client = static::createFreeWorkClient();
        $email = 'user-from-carriere-info@free-work.fr';

        $container = $client->getContainer();
        if (null === $container) {
            throw new \RuntimeException('Container is null');
        }
        $em = $container->get('doctrine')->getManager();
        $user = $em->getRepository(User::class)->findOneBy([
            'email' => $email,
        ]);
        self::assertNotNull($user);
        $oldPassword = $user->getPassword();
        self::assertNotNull($oldPassword);
        self::assertStringNotContainsString('argon', $oldPassword);

        $client->request('POST', '/login', [
            'json' => [
                'email' => $email,
                'password' => 'P@ssw0rd',
            ],
        ]);

        self::assertResponseIsSuccessful();

        $user = $em->getRepository(User::class)->findOneBy([
            'email' => $email,
        ]);
        self::assertNotNull($user);
        $newPassword = $user->getPassword();
        self::assertNotNull($newPassword);
        self::assertNotSame($oldPassword, $newPassword);
    }
}
