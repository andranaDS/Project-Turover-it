<?php

namespace App\JobPosting\Tests\Functional\JobPosting;

use App\Tests\Functional\ApiTestCase;

class JobPostingsSuggestedBannerGetTest extends ApiTestCase
{
    public static function provideWithSuggestedCases(): iterable
    {
        // Not Connected
        yield [
            null,
            [
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
                'hydra:totalItems' => 51,
            ],
        ];

        // Only freelance
        yield [
            'pablo.picasso@free-work.fr',
            [
                '@context' => '/contexts/JobPosting',
                '@id' => '/job_postings',
                '@type' => 'hydra:Collection',
                'hydra:member' => [
                    [
                        '@id' => '/job_postings/38',
                        '@type' => 'JobPosting',
                        'id' => 38,
                        'title' => 'Consultant en Poste de travail F/H',
                        'slug' => 'consultant-en-poste-de-travail-f-h',
                    ],
                    [
                        '@id' => '/job_postings/26',
                        '@type' => 'JobPosting',
                        'id' => 26,
                        'title' => 'Team Leader',
                        'slug' => 'team-leader',
                    ],
                ],
                'hydra:totalItems' => 2,
            ],
        ];

        // freelance && employee
        yield [
            'claude.monet@free-work.fr',
            [
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
                'hydra:totalItems' => 49,
            ],
        ];
    }

    /**
     * @dataProvider  provideWithSuggestedCases
     */
    public function testWithSuggested(?string $email, array $expected): void
    {
        if (null === $email) {
            $client = static::createFreeWorkClient();
        } else {
            $client = static::createFreeWorkAuthenticatedClient($email);
        }

        $client->request('GET', '/job_postings/suggested/banner');

        self::assertResponseIsSuccessful();
        self::assertJsonContains($expected);
    }
}
