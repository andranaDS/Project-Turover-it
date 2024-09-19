<?php

namespace App\Tests\Functional;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase as ApiPlatformTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\Core\Util\TokenGenerator;
use App\Recruiter\Security\AccessTokenUtils;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ApiTestCase extends ApiPlatformTestCase
{
    protected static array $freeWorkCookies = [];
    protected static array $turnoverCookies = [];

    protected static function createTurnoverClient(array $kernelOptions = [], array $defaultOptions = []): Client
    {
        $client = parent::createClient($kernelOptions, $defaultOptions);

        $container = $client->getContainer();

        $client->setDefaultOptions([
            'base_uri' => $container->getParameter('api_turnover_scheme') . '://' . $container->getParameter('api_turnover_base_url'),
            'headers' => [
                'accept' => 'application/ld+json',
                'accept-language' => 'fr',
            ],
        ]);

        return $client;
    }

    protected static function createTurnoverAuthenticatedClient(string $email = 'walter.white@breaking-bad.com', string $password = 'P@ssw0rd', bool $reload = false): Client
    {
        $cookie = static::getTurnoverCookie($email, $password, $reload);

        $client = static::createTurnoverClient();
        $client->getKernelBrowser()->getCookieJar()->set($cookie);

        return $client;
    }

    public static function getTurnoverCookie(string $email, string $password, bool $refresh = false)
    {
        $cookie = static::$turnoverCookies[$email] ?? null;
        if (false === $refresh && null !== $cookie) {
            return $cookie;
        }

        $cookie = new Cookie(
            AccessTokenUtils::$cookieName,
            TokenGenerator::generateFromValue($email, 32),
            Carbon::tomorrow()->getTimestamp()
        );

        static::$turnoverCookies[$email] = $cookie;

        return $cookie;
    }

    protected static function createFreeWorkClient(array $kernelOptions = [], array $defaultOptions = []): Client
    {
        $client = parent::createClient($kernelOptions, $defaultOptions);

        $container = $client->getContainer();

        $client->setDefaultOptions([
            'base_uri' => $container->getParameter('api_free_work_scheme') . '://' . $container->getParameter('api_free_work_base_url'),
            'headers' => [
                'accept' => 'application/ld+json',
                'accept-language' => 'fr',
            ],
        ]);

        return $client;
    }

    protected static function createFreeWorkAuthenticatedClient(string $email = 'user@free-work.fr', string $password = 'P@ssw0rd', bool $reload = false): Client
    {
        $cookies = static::getFreeWorkCookies($email, $password, $reload);

        $client = static::createFreeWorkClient();

        foreach ($cookies as $cookie) {
            $client->getKernelBrowser()->getCookieJar()->set($cookie);
        }

        return $client;
    }

    public static function createFreeWorkAuthenticatedUserClient(): Client
    {
        return self::createFreeWorkAuthenticatedClient();
    }

    public static function createFreeWorkAuthenticatedAdminClient(): Client
    {
        return self::createFreeWorkAuthenticatedClient('admin@free-work.fr');
    }

    public static function getFreeWorkCookies(string $email, string $password, bool $refresh = false)
    {
        $cookies = static::$freeWorkCookies[$email] ?? null;

        if (false === $refresh && null !== $cookies) {
            return $cookies;
        }

        $client = static::createFreeWorkClient();

        $response = $client->request('POST', '/login', [
            'json' => [
                'email' => $email,
                'password' => $password,
            ],
        ]);

        $cookies = array_filter(array_map(static function (string $cookie) {
            if (null === $cookie = explode(';', $cookie)[0] ?? null) {
                return null;
            }
            $cookieParts = explode('=', $cookie);
            $cookieName = $cookieParts[0] ?? null;
            $cookieValue = $cookieParts[1] ?? null;

            if (null === $cookieName || null === $cookieValue) {
                return null;
            }

            return new Cookie($cookieName, $cookieValue);
        }, $response->getHeaders()['set-cookie'] ?? []));

        static::$freeWorkCookies[$email] = $cookies;

        return $cookies;
    }

    public static function extractCookies(ResponseInterface $response): array
    {
        $cookies = $response->getHeaders()['set-cookie'] ?? [];
        sort($cookies);

        return array_map(static function (string $cookie) {
            $parts = array_map('trim', explode(';', $cookie));
            sort($parts);

            return substr($parts[0], 8);
        }, $cookies);
    }

    public function getEntityManager(Client $client): EntityManagerInterface
    {
        $container = $client->getContainer();
        if (null === $container) {
            throw new \RuntimeException('Container is null');
        }

        return $container->get('doctrine')->getManager();
    }

    public static function synchronizeElasticsearch(): void
    {
        $client = self::createClient();
        $application = new Application($client->getKernel());
        $command = $application->find('fos:elastic:populate');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            '--no-interaction',
        ]);
    }
}
