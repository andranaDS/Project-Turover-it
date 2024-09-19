<?php

namespace App\JobPosting\Tests\Functional\FreeWork\JobPosting;

use App\Tests\Functional\ApiTestCase;
use App\User\Entity\User;

class JobPostingsSuggestedGetTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/job_postings/suggested');

        self::assertResponseStatusCodeSame(401);
    }

    public function testWithSuggested(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');

        $container = $client->getContainer();

        if (null === $container) {
            throw new \RuntimeException('Container is null');
        }

        $em = $container->get('doctrine')->getManager();
        /* @var User $user */
        $user = $em->getRepository(User::class)->findOneBy([
            'email' => 'claude.monet@free-work.fr',
        ]);

        self::assertNotNull($user);
        self::assertCount(3, $user->getSkills());
        self::assertCount(2, $user->getJobs());
        self::assertSame(40000, $user->getGrossAnnualSalary());
        self::assertSame(300, $user->getAverageDailyRate());
        self::assertCount(2, $user->getLocations());
        self::assertTrue($user->getFulltimeTeleworking());
        self::assertIsArray($user->getContracts());
        self::assertSame('fixed-term', $user->getContracts()[0]);
        self::assertSame('permanent', $user->getContracts()[1]);

        $client->request('GET', '/job_postings/suggested');

        self::assertJsonContains([
            '@context' => '/contexts/JobPosting',
            '@id' => '/job_postings',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/job_postings/50',
                    '@type' => 'JobPosting',
                    'id' => 50,
                    'title' => 'Ingénieur de Production F/H',
                    'slug' => 'ingenieur-de-production-f-h-1',
                ],
                [
                    '@id' => '/job_postings/51',
                    '@type' => 'JobPosting',
                    'id' => 51,
                    'title' => 'Développeur Java 8 ans Exp - IDF - (H/F)',
                    'slug' => 'developpeur-java-8-ans-exp-idf-h-f',
                ],
            ],
            'hydra:totalItems' => 5,
            'hydra:view' => [
                '@id' => '/job_postings/suggested?page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/job_postings/suggested?page=1',
                'hydra:last' => '/job_postings/suggested?page=3',
                'hydra:next' => '/job_postings/suggested?page=2',
            ],
        ]);
    }
}
