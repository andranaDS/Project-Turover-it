<?php

namespace App\User\Tests\Functional\Recruiter;

use App\Tests\Functional\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class LastResumeViewedTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createTurnoverClient();
        $client->request('GET', '/users/last_viewed');
        self::assertResponseStatusCodeSame(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function testLastResumeViewed(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('GET', '/users/last_viewed');
        self::assertJsonContains([
            '@context' => '/contexts/User',
            '@id' => '/legacy/users',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/users/6',
                    '@type' => 'User',
                    'firstName' => 'Claude',
                    'lastName' => 'Monet',
                    'experienceYear' => '3-4_years',
                    'availability' => 'immediate',
                    'contracts' => [
                        'fixed-term',
                        'permanent',
                    ],
                    'anonymous' => false,
                    'createdAt' => '2020-01-01T10:00:00+01:00',
                    'updatedAt' => '2020-01-01T11:00:00+01:00',
                    'location' => [
                        '@type' => 'Location',
                        'street' => null,
                        'postalCode' => null,
                    ],
                    'skills' => [
                        [
                            '@type' => 'UserSkill',
                            'skill' => [
                                '@id' => '/skills/1',
                                '@type' => 'Skill',
                                'name' => 'php',
                            ],
                        ],
                        [
                            '@type' => 'UserSkill',
                            'skill' => [
                                '@id' => '/skills/2',
                                '@type' => 'Skill',
                                'name' => 'java',
                            ],
                        ],
                        [
                            '@type' => 'UserSkill',
                            'skill' => [
                                '@id' => '/skills/3',
                                '@type' => 'Skill',
                                'name' => 'javascript',
                            ],
                        ],
                    ],
                    'jobs' => [
                        [
                            '@type' => 'UserJob',
                            'job' => [
                                '@id' => '/jobs/analyste-realisateur',
                                '@type' => 'Job',
                                'name' => 'Analyste réalisateur',
                            ],
                        ],
                        [
                            '@type' => 'UserJob',
                            'job' => [
                                '@id' => '/jobs/developpeur-autre-langage-cobol-perl-vba-ruby-shell',
                                '@type' => 'Job',
                                'name' => 'Développeur',
                            ],
                        ],
                    ],
                    'formattedGrossAnnualSalary' => "40k\u{a0}€",
                    'formattedAverageDailyRate' => "300\u{a0}€",
                ],
            ],
            'hydra:totalItems' => 1,
        ]);
    }
}
