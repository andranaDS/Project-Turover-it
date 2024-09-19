<?php

namespace App\Recruiter\Tests\Functional\Turnover\ChangeEmail;

use App\Recruiter\Entity\Recruiter;
use App\Tests\Functional\ApiTestCase;

class RequestTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createTurnoverClient();
        $client->request('PATCH', '/change_email/request', [
            'json' => [
                'email' => 'dexter-morgan@dexter.com',
            ],
        ]);

        self::assertResponseStatusCodeSame(401);
    }

    public function testWithValidEmail(): void
    {
        $client = static::createTurnoverAuthenticatedClient('maeve.millay@ww.com');

        $container = $client->getContainer();
        if (null === $container) {
            throw new \RuntimeException('Container is null');
        }
        $em = $container->get('doctrine')->getManager();

        /** @var Recruiter $recruiter */
        $recruiter = $em->getRepository(Recruiter::class)->findOneBy([
            'email' => 'maeve.millay@ww.com',
        ]);

        self::assertNotNull($recruiter);

        self::assertSame('maeve.millay@ww.com', $recruiter->getEmail());
        self::assertNull($recruiter->getEmailRequestedAt());
        self::assertNull($recruiter->getConfirmationToken());

        $client->request('PATCH', '/change_email/request', [
            'json' => [
                'email' => 'mmillay@ww.com',
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertSame('maeve.millay@ww.com', $recruiter->getEmail());
        self::assertNotNull($recruiter->getEmailRequestedAt());
        self::assertNotNull($recruiter->getConfirmationToken());

        // email
        self::assertEmailCount(1);
        $email = self::getMailerMessage();
        self::assertNotNull($email);
        self::assertEmailHeaderSame($email, 'from', 'Turnover-IT <service_clients@turnover-it.com>');
        self::assertEmailHeaderSame($email, 'to', 'mmillay@ww.com');
        self::assertEmailHeaderSame($email, 'subject', 'TEST: Confirmez votre email');
        self::assertEmailTextBodyContains($email, 'Votre adresse email a besoin d\'être confirmée afin d\'utiliser l\'ensemble des fonctionnalités de Turnover-IT.');
        self::assertEmailTextBodyContains($email, 'CONFIRMER');
        self::assertEmailTextBodyContains($email, 'https://front.turnover-it.localhost/account/confirm-email/' . $recruiter->getConfirmationToken() . '/mmillay@ww.com');
    }

    public function testWithInvalidEmail(): void
    {
        $client = static::createTurnoverAuthenticatedClient('maeve.millay@ww.com');
        $client->request('PATCH', '/change_email/request', [
            'json' => [
                'email' => 'maeve.millay',
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
                    'message' => "Cette valeur n'est pas une adresse email valide.",
                ],
            ],
        ]);
    }

    public function testWithBlankEmail(): void
    {
        $client = static::createTurnoverAuthenticatedClient('maeve.millay@ww.com');
        $client->request('PATCH', '/change_email/request', [
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

    public function testWithAlreadyUsedEmail(): void
    {
        $client = static::createTurnoverAuthenticatedClient('maeve.millay@ww.com');
        $client->request('PATCH', '/change_email/request', [
            'json' => [
                'email' => 'dolores.abernathy@ww.com',
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
                    'message' => 'Cette valeur est déjà utilisée.',
                ],
            ],
        ]);
    }
}
