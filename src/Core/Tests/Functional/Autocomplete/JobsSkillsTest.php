<?php

namespace App\Core\Tests\Functional\Autocomplete;

use App\Tests\Functional\ApiTestCase;

class JobsSkillsTest extends ApiTestCase
{
    public function testWithoutSearch(): void
    {
        $client = static::createFreeWorkClient();
        $response = $client->request('GET', '/jobs_skills/autocomplete');

        self::assertResponseStatusCodeSame(200);
        self::assertResponseIsSuccessful();
        self::assertEmpty($response->toArray());
    }

    public static function provideWitchSearchCases(): iterable
    {
        yield [
            'php',
            [
                [
                    'name' => 'Développeur PHP (symfony, laravel, drupal ...)',
                    'slug' => 'developpeur-php-symfony-laravel-drupal',
                    'type' => 'job',
                ],
                [
                    'name' => 'php',
                    'slug' => 'php',
                    'type' => 'skill',
                ],
            ],
        ];
        yield [
            'java',
            [
                [
                    'name' => 'Développeur java (kotlin, groovy, scala...)',
                    'slug' => 'developpeur-java-kotlin-groovy-scala',
                    'type' => 'job',
                ],
                [
                    'name' => 'Développeur front end (javascript, node, react, angular, vue ...)',
                    'slug' => 'developpeur-front-end-javascript-node-react-angular-vue',
                    'type' => 'job',
                ],
                [
                    'name' => 'java',
                    'slug' => 'java',
                    'type' => 'skill',
                ],
                [
                    'name' => 'javascript',
                    'slug' => 'javascript',
                    'type' => 'skill',
                ],
            ],
        ];
    }

    /**
     * @dataProvider  provideWitchSearchCases
     */
    public function testWitchSearch(string $search, array $excepted): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/jobs_skills/autocomplete?q=' . $search);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');
        self::assertJsonContains($excepted);
    }
}
