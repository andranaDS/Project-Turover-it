<?php

namespace App\Resource\Tests\Functionnal\Trend;

use App\Core\Util\Dates;
use App\Tests\Functional\ApiTestCase;

class TrendGetTest extends ApiTestCase
{
    public static function provideLastCases(): iterable
    {
        yield ['last'];
        yield [Dates::lastWeek()->format('Y-m-d')];
    }

    /**
     * @dataProvider provideLastCases
     */
    public function testLast(string $date): void
    {
        $lastWeek = Dates::lastWeek()->format('Y-m-d');
        self::createFreeWorkClient()->request('GET', '/trends/' . $date);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/Trend',
            '@id' => '/trends/' . $lastWeek,
            '@type' => 'Trend',
            'date' => $lastWeek,
            'resumesCount' => 13000,
            'genderDistribution' => [
                'male' => [
                    'count' => 4,
                    'percentage' => 0.8,
                ],
                'female' => [
                    'count' => 1,
                    'percentage' => 0.2,
                ],
            ],
            'statusDistribution' => [
                'free' => [
                    'count' => 2,
                    'percentage' => 0.5,
                ],
                'work' => [
                    'count' => 2,
                    'percentage' => 0.5,
                ],
            ],
            'remoteDistribution' => [
                'false' => [
                    'count' => 2,
                    'percentage' => 0.4,
                ],
                'true' => [
                    'count' => 3,
                    'percentage' => 0.6,
                ],
            ],
            'candidateSkillsTable' => [
                '@id' => '/trend_skill_tables/3',
                '@type' => 'TrendSkillTable',
                'lines' => [
                    [
                        '@id' => '/trend_skill_lines/11',
                        '@type' => 'TrendSkillLine',
                        'skill' => [
                            '@id' => '/skills/4',
                            '@type' => 'Skill',
                            'id' => 4,
                            'name' => 'symfony',
                            'slug' => 'symfony',
                        ],
                        'position' => 1,
                        'evolution' => 0,
                        'count' => 3168,
                    ],
                    [
                        '@id' => '/trend_skill_lines/12',
                        '@type' => 'TrendSkillLine',
                        'skill' => [
                            '@id' => '/skills/5',
                            '@type' => 'Skill',
                            'id' => 5,
                            'name' => 'api platform',
                            'slug' => 'api-platform',
                        ],
                        'position' => 2,
                        'evolution' => 0,
                        'count' => 3136,
                    ],
                    [
                        '@id' => '/trend_skill_lines/13',
                        '@type' => 'TrendSkillLine',
                        'skill' => [
                            '@id' => '/skills/7',
                            '@type' => 'Skill',
                            'id' => 7,
                            'name' => 'docker',
                            'slug' => 'docker',
                        ],
                        'position' => 3,
                        'evolution' => 1,
                        'count' => 3105,
                    ],
                    [
                        '@id' => '/trend_skill_lines/14',
                        '@type' => 'TrendSkillLine',
                        'skill' => [
                            '@id' => '/skills/6',
                            '@type' => 'Skill',
                            'id' => 6,
                            'name' => 'laravel',
                            'slug' => 'laravel',
                        ],
                        'position' => 4,
                        'evolution' => -1,
                        'count' => 3074,
                    ],
                    [
                        '@id' => '/trend_skill_lines/15',
                        '@type' => 'TrendSkillLine',
                        'skill' => [
                            '@id' => '/skills/9',
                            '@type' => 'Skill',
                            'id' => 9,
                            'name' => 'flutter',
                            'slug' => 'flutter',
                        ],
                        'position' => 5,
                        'evolution' => 0,
                        'count' => 2983,
                    ],
                ],
            ],
            'recruiterSkillsTable' => [
                '@id' => '/trend_skill_tables/4',
                '@type' => 'TrendSkillTable',
                'lines' => [
                    [
                        '@id' => '/trend_skill_lines/16',
                        '@type' => 'TrendSkillLine',
                        'skill' => [
                            '@id' => '/skills/9',
                            '@type' => 'Skill',
                            'id' => 9,
                            'name' => 'flutter',
                            'slug' => 'flutter',
                        ],
                        'position' => 1,
                        'evolution' => 0,
                        'count' => 3167,
                    ],
                    [
                        '@id' => '/trend_skill_lines/17',
                        '@type' => 'TrendSkillLine',
                        'skill' => [
                            '@id' => '/skills/4',
                            '@type' => 'Skill',
                            'id' => 4,
                            'name' => 'symfony',
                            'slug' => 'symfony',
                        ],
                        'position' => 2,
                        'evolution' => 0,
                        'count' => 3135,
                    ],
                    [
                        '@id' => '/trend_skill_lines/18',
                        '@type' => 'TrendSkillLine',
                        'skill' => [
                            '@id' => '/skills/7',
                            '@type' => 'Skill',
                            'id' => 7,
                            'name' => 'docker',
                            'slug' => 'docker',
                        ],
                        'position' => 3,
                        'evolution' => 1,
                        'count' => 3104,
                    ],
                    [
                        '@id' => '/trend_skill_lines/19',
                        '@type' => 'TrendSkillLine',
                        'skill' => [
                            '@id' => '/skills/5',
                            '@type' => 'Skill',
                            'id' => 5,
                            'name' => 'api platform',
                            'slug' => 'api-platform',
                        ],
                        'position' => 4,
                        'evolution' => -1,
                        'count' => 3073,
                    ],
                    [
                        '@id' => '/trend_skill_lines/20',
                        '@type' => 'TrendSkillLine',
                        'skill' => [
                            '@id' => '/skills/6',
                            '@type' => 'Skill',
                            'id' => 6,
                            'name' => 'laravel',
                            'slug' => 'laravel',
                        ],
                        'position' => 5,
                        'evolution' => 0,
                        'count' => 2982,
                    ],
                ],
            ],
            'candidateJobsTable' => [
                '@id' => '/trend_job_tables/3',
                '@type' => 'TrendJobTable',
                'lines' => [
                    [
                        '@id' => '/trend_job_lines/11',
                        '@type' => 'TrendJobLine',
                        'job' => [
                            '@id' => '/jobs/lead-developer',
                            '@type' => 'Job',
                            'id' => 136,
                            'name' => 'Lead Developer',
                            'slug' => 'lead-developer',
                        ],
                        'position' => 1,
                        'evolution' => 0,
                        'count' => 3458,
                    ],
                    [
                        '@id' => '/trend_job_lines/12',
                        '@type' => 'TrendJobLine',
                        'job' => [
                            '@id' => '/jobs/product-owner',
                            '@type' => 'Job',
                            'id' => 141,
                            'name' => 'Product Owner',
                            'slug' => 'product-owner',
                        ],
                        'position' => 2,
                        'evolution' => 0,
                        'count' => 2864,
                    ],
                    [
                        '@id' => '/trend_job_lines/13',
                        '@type' => 'TrendJobLine',
                        'job' => [
                            '@id' => '/jobs/technicien-it',
                            '@type' => 'Job',
                            'id' => 166,
                            'name' => 'Technicien IT',
                            'slug' => 'technicien-it',
                        ],
                        'position' => 3,
                        'evolution' => 0,
                        'count' => 2606,
                    ],
                    [
                        '@id' => '/trend_job_lines/14',
                        '@type' => 'TrendJobLine',
                        'job' => [
                            '@id' => '/jobs/integrateur-web-html-css',
                            '@type' => 'Job',
                            'id' => 180,
                            'name' => 'Web Developer',
                            'slug' => 'web-developer',
                        ],
                        'position' => 4,
                        'evolution' => 0,
                        'count' => 2371,
                    ],
                    [
                        '@id' => '/trend_job_lines/15',
                        '@type' => 'TrendJobLine',
                        'job' => [
                            '@id' => '/jobs/responsable-technique',
                            '@type' => 'Job',
                            'id' => 159,
                            'name' => 'Responsable Technique',
                            'slug' => 'responsable-technique',
                        ],
                        'position' => 5,
                        'evolution' => 0,
                        'count' => 1964,
                    ],
                ],
            ],
            'recruiterJobsTable' => [
                '@id' => '/trend_job_tables/4',
                '@type' => 'TrendJobTable',
                'lines' => [
                    [
                        '@id' => '/trend_job_lines/16',
                        '@type' => 'TrendJobLine',
                        'job' => [
                            '@id' => '/jobs/responsable-technique',
                            '@type' => 'Job',
                            'id' => 159,
                            'name' => 'Responsable Technique',
                            'slug' => 'responsable-technique',
                            'availableForContribution' => true,
                            'nameForContributionSlug' => 'responsable-technique',
                        ],
                        'position' => 1,
                        'evolution' => 4,
                        'count' => 606720,
                    ],
                    [
                        '@id' => '/trend_job_lines/17',
                        '@type' => 'TrendJobLine',
                        'job' => [
                            '@id' => '/jobs/lead-developer',
                            '@type' => 'Job',
                            'id' => 136,
                            'name' => 'Lead Developer',
                            'slug' => 'lead-developer',
                            'availableForContribution' => true,
                            'nameForContributionSlug' => 'lead-developer',
                        ],
                        'position' => 2,
                        'evolution' => -1,
                        'count' => 582451,
                    ],
                    [
                        '@id' => '/trend_job_lines/18',
                        '@type' => 'TrendJobLine',
                        'job' => [
                            '@id' => '/jobs/product-owner',
                            '@type' => 'Job',
                            'id' => 141,
                            'name' => 'Product Owner',
                            'slug' => 'product-owner',
                            'availableForContribution' => true,
                            'nameForContributionSlug' => 'product-owner',
                        ],
                        'position' => 3,
                        'evolution' => -1,
                        'count' => 559153,
                    ],
                    [
                        '@id' => '/trend_job_lines/19',
                        '@type' => 'TrendJobLine',
                        'job' => [
                            '@id' => '/jobs/technicien-it',
                            '@type' => 'Job',
                            'id' => 166,
                            'name' => 'Technicien IT',
                            'slug' => 'technicien-it',
                            'availableForContribution' => false,
                            'nameForContributionSlug' => 'technicien-it',
                        ],
                        'position' => 4,
                        'evolution' => -1,
                        'count' => 474915,
                    ],
                    [
                        '@id' => '/trend_job_lines/20',
                        '@type' => 'TrendJobLine',
                        'job' => [
                            '@id' => '/jobs/integrateur-web-html-css',
                            '@type' => 'Job',
                            'id' => 180,
                            'name' => 'Web Developer',
                            'slug' => 'web-developer',
                            'availableForContribution' => true,
                            'nameForContributionSlug' => 'integrateur-web-html-css',
                        ],
                        'position' => 5,
                        'evolution' => -1,
                        'count' => 371743,
                    ],
                ],
            ],
        ]);
    }

    public function testNoContent(): void
    {
        self::createFreeWorkClient()->request('GET', '/trends/1990-01-01');

        self::assertResponseStatusCodeSame(404);
    }
}
