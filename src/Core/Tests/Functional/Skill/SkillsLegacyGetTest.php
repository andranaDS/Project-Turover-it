<?php

namespace App\Core\Tests\Functional\Skill;

use App\Tests\Functional\ApiTestCase;

class SkillsLegacyGetTest extends ApiTestCase
{
    public function testLoggedAsTurnover(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/legacy/skills', [
            'headers' => [
                'X-AUTH-TOKEN' => $_ENV['TURNOVER_IT_API_KEY'],
            ],
        ]);

        self::assertResponseStatusCodeSame(200);
        self::assertJsonContains([
            '@context' => '/contexts/Skill',
            '@id' => '/skills',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/skills/1',
                    '@type' => 'Skill',
                    'id' => 1,
                    'name' => 'php',
                ],
                [
                    '@id' => '/skills/2',
                    '@type' => 'Skill',
                    'id' => 2,
                    'name' => 'java',
                ],
            ],
        ]);
    }
}
