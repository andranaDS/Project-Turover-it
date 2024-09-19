<?php

namespace App\Core\Tests\Functional\Skill;

use App\Core\Entity\Skill;
use App\Tests\Functional\ApiTestCase;
use Doctrine\ORM\EntityManagerInterface;

class SkillsGetTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/skills');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('GET', '/skills');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('GET', '/skills');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testAutocompleteWithResult(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/skills?q=java');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        self::assertJsonContains([
            '@context' => '/contexts/Skill',
            '@id' => '/skills',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/skills/2',
                    '@type' => 'Skill',
                    'id' => 2,
                    'name' => 'java',
                    'slug' => 'java',
                    'jobUsageCount' => 7100,
                ],
                [
                    '@id' => '/skills/3',
                    '@type' => 'Skill',
                    'id' => 3,
                    'name' => 'javascript',
                    'slug' => 'javascript',
                    'jobUsageCount' => 2150,
                ],
            ],
            'hydra:totalItems' => 2,
        ]);
    }

    public function testAutocompleteWithoutResult(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/skills?q=without-result');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        self::assertJsonContains([
            '@context' => '/contexts/Skill',
            '@id' => '/skills',
            '@type' => 'hydra:Collection',
            'hydra:member' => [],
            'hydra:totalItems' => 0,
        ]);
    }

    public function testAutocompleteNoDisplayed(): void
    {
        $client = static::createFreeWorkClient();

        $container = $client->getContainer();
        if (null === $container) {
            throw new \RuntimeException('Container is null');
        }
        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine')->getManager();

        $skill = $em->getRepository(Skill::class)->findOneBy([
            'name' => 'assembly',
        ]);
        self::assertNotNull($skill);
        self::assertFalse($skill->getDisplayed());

        $client->request('GET', '/skills?q=assembly');
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        self::assertJsonContains([
            '@context' => '/contexts/Skill',
            '@id' => '/skills',
            '@type' => 'hydra:Collection',
            'hydra:member' => [],
            'hydra:totalItems' => 0,
        ]);
    }

    public function testWithResultAndOrder(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/skills?order[jobUsageCount]=desc');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        self::assertJsonContains([
            '@context' => '/contexts/Skill',
            '@id' => '/skills',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/skills/2',
                    '@type' => 'Skill',
                    'id' => 2,
                    'name' => 'java',
                    'slug' => 'java',
                    'jobUsageCount' => 7100,
                ],
                [
                    '@id' => '/skills/1',
                    '@type' => 'Skill',
                    'id' => 1,
                    'name' => 'php',
                    'slug' => 'php',
                    'jobUsageCount' => 3000,
                ],
            ],
            'hydra:totalItems' => 9,
        ]);
    }
}
