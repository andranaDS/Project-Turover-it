<?php

namespace App\Recruiter\Tests\Functional\Turnover\ChangeEmail;

use App\Recruiter\Entity\Recruiter;
use App\Tests\Functional\ApiTestCase;
use App\User\Entity\User;

class ConfirmTest extends ApiTestCase
{
    public function testWithoutToken(): void
    {
        $client = static::createTurnoverClient();

        $client->request('PATCH', '/change_email/confirm', [
            'json' => [],
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertJsonContains([
            'hydra:title' => 'Le lien de confirmation est expiré.',
        ]);
    }

    public function testWithNotFoundToken(): void
    {
        $client = static::createTurnoverClient();

        $client->request('PATCH', '/change_email/confirm', [
            'json' => [
                'token' => 'token-not-found',
            ],
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertJsonContains([
            'hydra:title' => 'Le lien de confirmation est expiré.',
        ]);
    }

    public function testWithExpiredToken(): void
    {
        $client = static::createTurnoverClient();

        $client->request('PATCH', '/change_email/confirm', [
            'json' => [
                'email' => 'hduflot@le-bureau-des-legendes.fr',
                'token' => 'e0365093d64b6f5d97c9',
            ],
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertJsonContains([
            'hydra:title' => 'Le lien de confirmation est expiré.',
        ]);
    }

    public function testWithActiveTokenAndWrongEmail(): void
    {
        $client = static::createTurnoverClient();

        $client->request('PATCH', '/change_email/confirm', [
            'json' => [
                'email' => 'gdebailly@le-bureau-des-legendes.fr',
                'token' => '4fd78135d0dbb7cb3199',
            ],
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertJsonContains([
            'hydra:title' => 'Le lien de confirmation est corrompu.',
        ]);
    }

    public function testWithActiveTokenAndGoodEmailAndLogged(): void
    {
        $client = static::createTurnoverAuthenticatedClient('guillaume.debailly@le-bureau-des-legendes.fr');

        // 0. check if the user is connected
        $client->request('GET', '/recruiters/16');
        self::assertResponseStatusCodeSame(200);

        $container = $client->getContainer();
        if (null === $container) {
            throw new \RuntimeException('Container is null');
        }
        $em = $container->get('doctrine')->getManager();

        // 1. before
        /** @var Recruiter $recruiter */
        $recruiter = $em->find(Recruiter::class, 16);
        self::assertNotNull($recruiter);
        self::assertSame('guillaume.debailly@le-bureau-des-legendes.fr', $recruiter->getEmail());
        self::assertSame('4fd78135d0dbb7cb3199', $recruiter->getConfirmationToken());
        self::assertNotNull($recruiter->getEmailRequestedAt());

        // 2. change email
        $client->request('PATCH', '/change_email/confirm', [
            'json' => [
                'email' => 'g.debailly@le-bureau-des-legendes.fr',
                'token' => '4fd78135d0dbb7cb3199',
            ],
        ]);
        self::assertResponseIsSuccessful();

        // 3. after
        /** @var Recruiter $recruiter */
        $recruiter = $em->find(Recruiter::class, 16);
        self::assertNotNull($recruiter);
        self::assertSame('g.debailly@le-bureau-des-legendes.fr', $recruiter->getEmail());
        self::assertNull($recruiter->getConfirmationToken());
        self::assertNull($recruiter->getEmailRequestedAt());

        // 4. check if the user is disconnected
        $client->request('GET', '/recruiters/16');
        self::assertResponseStatusCodeSame(401);
    }

    public function testWithActiveTokenAndGoodEmailAndNotLogged(): void
    {
        $client = static::createTurnoverClient();

        // 0. check if the user is connected
        $client->request('GET', '/recruiters/16');
        self::assertResponseStatusCodeSame(401);

        $container = $client->getContainer();
        if (null === $container) {
            throw new \RuntimeException('Container is null');
        }
        $em = $container->get('doctrine')->getManager();

        // 1. before
        /** @var Recruiter $recruiter */
        $recruiter = $em->find(Recruiter::class, 16);
        self::assertNotNull($recruiter);
        self::assertSame('guillaume.debailly@le-bureau-des-legendes.fr', $recruiter->getEmail());
        self::assertSame('4fd78135d0dbb7cb3199', $recruiter->getConfirmationToken());
        self::assertNotNull($recruiter->getEmailRequestedAt());

        // 2. change email
        $client->request('PATCH', '/change_email/confirm', [
            'json' => [
                'email' => 'g.debailly@le-bureau-des-legendes.fr',
                'token' => '4fd78135d0dbb7cb3199',
            ],
        ]);
        self::assertResponseIsSuccessful();

        // 3. after
        /** @var Recruiter $recruiter */
        $recruiter = $em->find(Recruiter::class, 16);
        self::assertNotNull($recruiter);
        self::assertSame('g.debailly@le-bureau-des-legendes.fr', $recruiter->getEmail());
        self::assertNull($recruiter->getConfirmationToken());
        self::assertNull($recruiter->getEmailRequestedAt());

        // 4. check if the user is disconnected
        $client->request('GET', '/recruiters/16');
        self::assertResponseStatusCodeSame(401);
    }
}
