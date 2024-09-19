<?php

namespace App\Recruiter\Tests\Functional\Turnover\ForgottenPassword;

use App\Recruiter\Entity\Recruiter;
use App\Tests\Functional\ApiTestCase;

class ResetTest extends ApiTestCase
{
    public function testWithActiveTokenAndInvalidPassword(): void
    {
        $client = static::createTurnoverClient();

        $client->request('POST', '/forgotten_password/reset', [
            'json' => [
                'token' => 'forgotten-password-valid-token',
                'plainPassword' => 'password',
            ],
        ]);

        self::assertJsonContains([
            '@context' => '/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'violations' => [
                [
                    'propertyPath' => 'plainPassword',
                    'message' => 'La force du mot de passe doit être au minimum "Bon".',
                ],
            ],
        ]);

        self::assertResponseStatusCodeSame(422);
    }

    public function testWithoutToken(): void
    {
        $client = static::createTurnoverClient();

        $client->request('POST', '/forgotten_password/reset', [
            'json' => [],
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertJsonContains([
            'hydra:title' => 'Le lien de réinitialisation est expiré.',
        ]);
    }

    public function testWithNotFoundToken(): void
    {
        $client = static::createTurnoverClient();

        $client->request('POST', '/forgotten_password/reset', [
            'json' => [
                'token' => 'forgotten-password-token-not-found',
            ],
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertJsonContains([
            'hydra:title' => 'Le lien de réinitialisation est expiré.',
        ]);
    }

    public function testWithExpiredToken(): void
    {
        $client = static::createTurnoverClient();

        $client->request('POST', '/forgotten_password/reset', [
            'json' => [
                'token' => 'forgotten-password-expired-token',
            ],
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertJsonContains([
            'hydra:title' => 'Le lien de réinitialisation est expiré.',
        ]);
    }

    public function testWithActiveTokenAndWithoutPassword(): void
    {
        $client = static::createTurnoverClient();
        $em = $this->getEntityManager($client);

        $confirmationToken = 'forgotten-password-valid-token';

        /** @var Recruiter $recruiter */
        $recruiter = $em->getRepository(Recruiter::class)->findOneByConfirmationToken([
            'confirmationToken' => $confirmationToken,
        ]);

        $oldPassword = $recruiter->getPassword();

        self::assertNotNull($recruiter);

        $client->request('POST', '/forgotten_password/reset', [
            'json' => [
                'token' => $confirmationToken,
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            '@context' => '/contexts/Recruiter',
            '@id' => '/recruiters/13',
            '@type' => 'Recruiter',
            'id' => 13,
            'email' => 'teddy.flood@ww.com',
            'username' => 'tflood',
            'gender' => null,
            'firstName' => 'Teddy',
            'lastName' => 'Flood',
            'phoneNumber' => null,
            'enabled' => false,
            'company' => [
                '@id' => '/companies/company-3',
                '@type' => 'Company',
                'id' => 3,
                'name' => 'Company 3',
                'slug' => 'company-3',
                'businessActivity' => '/company_business_activities/2',
                'billingAddress' => [
                    '@type' => 'Location',
                    'countryCode' => 'FR',
                ],
                'registrationNumber' => null,
            ],
            'site' => null,
            'main' => false,
            'job' => 'Cow-boy',
            'termsOfService' => false,
            'passwordUpdateRequired' => false,
        ]);

        // check if the password has been updated
        $newPassword = $recruiter->getPassword();
        self::assertSame($oldPassword, $newPassword);
    }

    public function testWithActiveTokenAndValidPassword(): void
    {
        $client = static::createTurnoverClient();
        $em = $this->getEntityManager($client);

        $confirmationToken = 'forgotten-password-valid-token';

        /** @var Recruiter $recruiter */
        $recruiter = $em->getRepository(Recruiter::class)->findOneByConfirmationToken([
            'confirmationToken' => $confirmationToken,
        ]);

        $oldPassword = $recruiter->getPassword();

        self::assertNotNull($recruiter);

        $client->request('POST', '/forgotten_password/reset', [
            'json' => [
                'token' => $confirmationToken,
                'plainPassword' => 'NewP@ssw0rd1',
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            '@context' => '/contexts/Recruiter',
            '@id' => '/recruiters/13',
            '@type' => 'Recruiter',
            'id' => 13,
            'email' => 'teddy.flood@ww.com',
            'username' => 'tflood',
            'gender' => null,
            'firstName' => 'Teddy',
            'lastName' => 'Flood',
            'phoneNumber' => null,
            'enabled' => true,
            'company' => [
                '@id' => '/companies/company-3',
                '@type' => 'Company',
                'id' => 3,
                'name' => 'Company 3',
                'slug' => 'company-3',
                'businessActivity' => '/company_business_activities/2',
                'billingAddress' => [
                    '@type' => 'Location',
                    'countryCode' => 'FR',
                ],
                'registrationNumber' => null,
            ],
            'site' => null,
            'main' => false,
            'job' => 'Cow-boy',
            'termsOfService' => false,
            'passwordUpdateRequired' => false,
        ]);

        // check if the password has been updated
        $newPassword = $recruiter->getPassword();
        self::assertNotSame($oldPassword, $newPassword);

        // email
        self::assertEmailCount(1);
        $email = self::getMailerMessage();
        self::assertNotNull($email);
        self::assertEmailHeaderSame($email, 'from', 'Turnover-IT <service_clients@turnover-it.com>');
        self::assertEmailHeaderSame($email, 'to', 'teddy.flood@ww.com');
        self::assertEmailHeaderSame($email, 'subject', 'TEST: Votre mot de passe a été réinitalisé');
        self::assertEmailTextBodyContains($email, 'Votre mot de passe a bien été réinitialisé.');
        self::assertEmailTextBodyContains($email, 'Vous pouvez maintenant l\'utiliser pour accéder à votre compte.');
        self::assertEmailTextBodyContains($email, 'Vous pouvez dès à présent vous connecter avec votre nouveau mot de passe :');
        self::assertEmailTextBodyContains($email, 'ME CONNECTER');
        self::assertEmailTextBodyContains($email, 'https://front.turnover-it.localhost/');
    }
}
