<?php

namespace App\Recruiter\Tests\Functional\Turnover\Registration;

use App\Recruiter\Entity\Recruiter;
use App\Tests\Functional\ApiTestCase;

class ConfirmTest extends ApiTestCase
{
    public function testWithoutToken(): void
    {
        $client = static::createTurnoverClient();

        $client->request('POST', '/registration/confirm', [
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

        $em = $client->getContainer()->get('doctrine')->getManager();

        $token = 'token-not-found';

        // check if the token exists in the database
        $recruiter = $em->getRepository(Recruiter::class)->findOneByConfirmationToken($token);
        self::assertNull($recruiter);

        $client->request('POST', '/registration/confirm', [
            'json' => [
                'token' => 'token-email-not-found',
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

        $em = $client->getContainer()->get('doctrine')->getManager();

        $token = 'carrie-mathison-token';

        // check if the token exists in the database
        $recruiter = $em->getRepository(Recruiter::class)->findOneByConfirmationToken($token);
        self::assertNotNull($recruiter);

        // try yo confirm the email
        $client = static::createTurnoverClient();
        $client->request('POST', '/registration/confirm', [
            'json' => [
                'token' => $token,
            ],
        ]);
        self::assertResponseStatusCodeSame(422);
        self::assertJsonContains([
            'hydra:title' => 'Le lien de confirmation est expiré.',
        ]);
    }

    public function testWithValidToken(): void
    {
        $client = static::createTurnoverClient();

        $client->request('POST', '/registration/confirm', [
            'json' => [
                'token' => 'peter-quinn-token',
            ],
        ]);

        // response
        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            'email' => 'peter.quinn@homeland.com',
            'enabled' => true,
        ]);

        // email
        $email = self::getMailerMessage();
        self::assertNotNull($email);
        self::assertEmailHeaderSame($email, 'from', 'Turnover-IT <service_clients@turnover-it.com>');
        self::assertEmailHeaderSame($email, 'to', 'peter.quinn@homeland.com');
        self::assertEmailHeaderSame($email, 'subject', 'TEST: Votre email est confirmé !');
        self::assertEmailTextBodyContains($email, 'Votre email est confirmé, vous pouvez dès à présent utiliser l\'ensemble des fonctionnalités de Turnover-IT !');
        self::assertEmailTextBodyContains($email, 'ME CONNECTER');
        self::assertEmailTextBodyContains($email, 'https://front.turnover-it.localhost/login');
    }
}
