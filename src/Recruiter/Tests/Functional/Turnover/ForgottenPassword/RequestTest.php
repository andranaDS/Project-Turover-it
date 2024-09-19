<?php

namespace App\Recruiter\Tests\Functional\Turnover\ForgottenPassword;

use App\Recruiter\Entity\Recruiter;
use App\Tests\Functional\ApiTestCase;

class RequestTest extends ApiTestCase
{
    public function testWithExistingEmailAndWithoutRequest(): void
    {
        $client = static::createTurnoverClient();
        $em = $this->getEntityManager($client);

        $email = 'robert.ford@ww.com';

        /** @var Recruiter $recruiter */
        $recruiter = $em->getRepository(Recruiter::class)->findOneBy([
            'email' => $email,
        ]);

        self::assertNotNull($recruiter);

        self::assertNull($recruiter->getPasswordRequestedAt());
        self::assertNull($recruiter->getConfirmationToken());

        $client->request('POST', '/forgotten_password/request', [
            'json' => [
                'email' => $email,
            ],
        ]);

        self::assertResponseIsSuccessful();

        self::assertNotNull($recruiter->getPasswordRequestedAt());
        self::assertNotNull($recruiter->getConfirmationToken());

        // email
        self::assertEmailCount(1);
        $email = self::getMailerMessage();
        self::assertNotNull($email);
        self::assertEmailHeaderSame($email, 'from', 'Turnover-IT <service_clients@turnover-it.com>');
        self::assertEmailHeaderSame($email, 'to', 'robert.ford@ww.com');
        self::assertEmailHeaderSame($email, 'subject', 'TEST: Demande de nouveau mot de passe');
        self::assertEmailTextBodyContains($email, 'Nous avons reçu votre demande de nouveau mot de passe.');
        self::assertEmailTextBodyContains($email, 'Renouvelez-le en un clic à l\'aide du bouton suivant :');
        self::assertEmailTextBodyContains($email, 'Changer de mot de passe');
        self::assertEmailTextBodyContains($email, 'https://front.turnover-it.localhost/password/reset/' . $recruiter->getConfirmationToken());
    }

    public function testWithExistingEmailAndExpiredRequest(): void
    {
        $client = static::createTurnoverClient();
        $em = $this->getEntityManager($client);

        $email = 'bernard.lowe@ww.com';

        /** @var Recruiter $recruiter */
        $recruiter = $em->getRepository(Recruiter::class)->findOneBy([
            'email' => $email,
        ]);

        self::assertNotNull($recruiter);

        $oldPasswordRequestedAt = $recruiter->getPasswordRequestedAt();
        $oldConfirmationToken = $recruiter->getConfirmationToken();

        self::assertNotNull($oldPasswordRequestedAt);
        self::assertNotNull($oldConfirmationToken);

        $client->request('POST', '/forgotten_password/request', [
            'json' => [
                'email' => $email,
            ],
        ]);

        self::assertResponseIsSuccessful();

        $newPasswordRequestedAt = $recruiter->getPasswordRequestedAt();
        $newConfirmationToken = $recruiter->getConfirmationToken();

        self::assertNotNull($newPasswordRequestedAt);
        self::assertNotNull($newConfirmationToken);
        self::assertNotSame($oldPasswordRequestedAt->getTimestamp(), $newPasswordRequestedAt->getTimestamp());
        self::assertNotSame($oldConfirmationToken, $newConfirmationToken);

        // email
        self::assertEmailCount(1);
    }

    public function testWithExistingEmailAndActiveRequest(): void
    {
        $client = static::createTurnoverClient();
        $em = $this->getEntityManager($client);

        $email = 'teddy.flood@ww.com';

        /** @var Recruiter $recruiter */
        $recruiter = $em->getRepository(Recruiter::class)->findOneBy([
            'email' => $email,
        ]);

        self::assertNotNull($recruiter);

        $oldPasswordRequestedAt = $recruiter->getPasswordRequestedAt();
        $oldConfirmationToken = $recruiter->getConfirmationToken();

        self::assertNotNull($oldPasswordRequestedAt);
        self::assertNotNull($oldConfirmationToken);

        $client->request('POST', '/forgotten_password/request', [
            'json' => [
                'email' => $email,
            ],
        ]);

        self::assertResponseIsSuccessful();

        $newPasswordRequestedAt = $recruiter->getPasswordRequestedAt();
        $newConfirmationToken = $recruiter->getConfirmationToken();

        self::assertNotNull($newPasswordRequestedAt);
        self::assertNotNull($newConfirmationToken);
        self::assertSame($oldPasswordRequestedAt->getTimestamp(), $newPasswordRequestedAt->getTimestamp());
        self::assertSame($oldConfirmationToken, $newConfirmationToken);

        self::assertEmailCount(0);
    }

    public function testWithNotExistingEmail(): void
    {
        $client = static::createTurnoverClient();

        $container = $client->getContainer();
        if (null === $container) {
            throw new \RuntimeException('Container is null');
        }
        $em = $container->get('doctrine')->getManager();

        $email = 'jack.bauer@24.com';

        /** @var ?Recruiter $recruiter */
        $recruiter = $em->getRepository(Recruiter::class)->findOneBy([
            'email' => $email,
        ]);

        self::assertNull($recruiter);

        $client->request('POST', '/forgotten_password/request', [
            'json' => [
                'email' => $email,
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertEmailCount(0);
    }

    public function testWithInvalidEmail(): void
    {
        $client = static::createTurnoverClient();

        $client->request('POST', '/forgotten_password/request', [
            'json' => [
                'email' => 'invalid-email',
            ],
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertJsonContains([
            '@context' => '/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'hydra:description' => 'email: Cette valeur n\'est pas une adresse email valide.',
            'violations' => [
                [
                    'propertyPath' => 'email',
                    'message' => 'Cette valeur n\'est pas une adresse email valide.',
                ],
            ],
        ]);
    }

    public function testWithEmptyEmail(): void
    {
        $client = static::createTurnoverClient();

        $client->request('POST', '/forgotten_password/request', [
            'json' => [
                'email' => '',
            ],
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertJsonContains([
            '@context' => '/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'violations' => [
                [
                    'propertyPath' => 'email',
                    'message' => 'Cette valeur ne doit pas être vide.',
                ],
            ],
        ]);
    }
}
