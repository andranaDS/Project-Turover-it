<?php

namespace App\Recruiter\Tests\Functional\Turnover\Recruiter;

use App\Recruiter\Entity\Recruiter;
use App\Tests\Functional\ApiTestCase;
use Symfony\Component\BrowserKit\Cookie;

class PostItemTest extends ApiTestCase
{
    public static function provideValidCases(): iterable
    {
        return [
            'with_optional_properties' => [
                [
                    'email' => 'dustin.henderson@stranger-things.com',
                    'firstName' => 'Dustin',
                    'lastName' => 'Henderson',
                    'phoneNumber' => '+33601020304',
                    'company' => [
                        'name' => 'Stranger Things',
                        'businessActivity' => '/company_business_activities/1',
                        'registrationNumber' => '75029882000028',
                        'billingAddress' => [
                            'countryCode' => 'FR',
                        ],
                    ],
                    'job' => 'RECRUITER IT',
                    'termsOfService' => true,
                    'plainPassword' => '1P@ssw0rd!',
                ],
                [
                    '@context' => '/contexts/Recruiter',
                    '@type' => 'Recruiter',
                    'email' => 'dustin.henderson@stranger-things.com',
                    'username' => 'dustin.henderson@stranger-things.com',
                    'firstName' => 'Dustin',
                    'lastName' => 'Henderson',
                    'phoneNumber' => '+33601020304',
                    'enabled' => false,
                    'company' => [
                        '@type' => 'Company',
                        'name' => 'Stranger Things',
                        'slug' => 'stranger-things',
                        'registrationNumber' => '75029882000028',
                        'businessActivity' => '/company_business_activities/1',
                        'billingAddress' => [
                            'countryCode' => 'FR',
                            'country' => 'France',
                        ],
                    ],
                    'main' => true,
                    'job' => 'Recruiter IT',
                    'termsOfService' => true,
                    'passwordUpdateRequired' => false,
                ],
            ],
            'without_optional_properties_null' => [
                [
                    'email' => 'dustin.henderson@stranger-things.com',
                    'firstName' => 'Dustin',
                    'lastName' => 'Henderson',
                    'phoneNumber' => '+33601020304',
                    'company' => [
                        'name' => 'Stranger Things',
                        'businessActivity' => null,
                        'billingAddress' => [
                            'countryCode' => null,
                        ],
                        'registrationNumber' => null,
                    ],
                    'job' => null,
                    'termsOfService' => true,
                    'plainPassword' => '1P@ssw0rd!',
                ],
                [
                    '@context' => '/contexts/Recruiter',
                    '@type' => 'Recruiter',
                    'email' => 'dustin.henderson@stranger-things.com',
                    'username' => 'dustin.henderson@stranger-things.com',
                    'firstName' => 'Dustin',
                    'lastName' => 'Henderson',
                    'phoneNumber' => '+33601020304',
                    'enabled' => false,
                    'company' => [
                        '@type' => 'Company',
                        'name' => 'Stranger Things',
                        'slug' => 'stranger-things',
                        'billingAddress' => [
                            'countryCode' => null,
                            'country' => null,
                        ],
                        'registrationNumber' => null,
                        'businessActivity' => null,
                    ],
                    'main' => true,
                    'job' => null,
                    'termsOfService' => true,
                    'passwordUpdateRequired' => false,
                ],
            ],
            'without_optional_properties_empty' => [
                [
                    'email' => 'dustin.henderson@stranger-things.com',
                    'firstName' => 'Dustin',
                    'lastName' => 'Henderson',
                    'phoneNumber' => '+33601020304',
                    'company' => [
                        'name' => 'Stranger Things',
                        'businessActivity' => null,
                        'billingAddress' => [
                            'countryCode' => null,
                            'country' => null,
                        ],
                        'registrationNumber' => '',
                    ],
                    'job' => '',
                    'termsOfService' => true,
                    'plainPassword' => '1P@ssw0rd!',
                ],
                [
                    '@context' => '/contexts/Recruiter',
                    '@type' => 'Recruiter',
                    'email' => 'dustin.henderson@stranger-things.com',
                    'username' => 'dustin.henderson@stranger-things.com',
                    'firstName' => 'Dustin',
                    'lastName' => 'Henderson',
                    'phoneNumber' => '+33601020304',
                    'enabled' => false,
                    'company' => [
                        '@type' => 'Company',
                        'name' => 'Stranger Things',
                        'slug' => 'stranger-things',
                        'billingAddress' => [
                            'countryCode' => null,
                            'country' => null,
                        ],
                        'registrationNumber' => '',
                        'businessActivity' => null,
                    ],
                    'main' => true,
                    'job' => '',
                    'termsOfService' => true,
                    'passwordUpdateRequired' => false,
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideValidCases
     */
    public function testValidCases(array $payload, array $expected): void
    {
        $client = static::createTurnoverClient();
        $response = $client->request('POST', '/recruiters', [
            'json' => $payload,
        ]);

        self::assertResponseStatusCodeSame(201);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains($expected);

        $cookie = Cookie::fromString($response->getHeaders()['set-cookie'][0]);
        self::assertEqualsWithDelta(86400, $cookie->getExpiresTime() - time(), 2);

        $recruiterData = $response->toArray();

        $container = $client->getContainer();
        if (null === $container) {
            throw new \RuntimeException('Container is null');
        }
        $recruiter = $container->get('doctrine')->getManager()->find(Recruiter::class, $recruiterData['id']);
        $confirmationToken = $recruiter->getConfirmationToken();

        // check registration email
        $email = self::getMailerMessage();
        self::assertNotNull($email);
        self::assertEmailHeaderSame($email, 'from', 'Turnover-IT <service_clients@turnover-it.com>');
        self::assertEmailHeaderSame($email, 'to', $payload['email']);
        self::assertEmailHeaderSame($email, 'subject', 'TEST: Bienvenue sur Turnover-IT !');
        self::assertEmailTextBodyContains($email, 'Bienvenue sur Turnover-it.com, la plateforme de recrutement 100% IT !');
        self::assertEmailTextBodyContains($email, 'Cliquez sur le lien suivant afin de confirmer votre compte :');
        self::assertEmailTextBodyContains($email, 'CONFIRMER');
        self::assertEmailTextBodyContains($email, 'https://front.turnover-it.localhost/register/confirm-email/' . $confirmationToken);

        // check if the user created is logged
        $client->request('GET', '/recruiters/me');
        self::assertResponseStatusCodeSame(200);
    }

    public static function provideInvalidCases(): iterable
    {
        return [
            'empty' => [
                [
                    'email' => '',
                    'firstName' => '',
                    'lastName' => '',
                    'phoneNumber' => '',
                    'company' => [
                        'name' => '',
                        'businessActivity' => null,
                        'billingAddress' => [
                            'countryCode' => '',
                        ],
                        'registrationNumber' => '',
                    ],
                    'job' => '',
                    'termsOfService' => false,
                    'plainPassword' => '',
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'email',
                            'message' => 'Cette valeur ne doit pas être vide.',
                        ],
                        [
                            'propertyPath' => 'username',
                            'message' => 'Cette valeur ne doit pas être vide.',
                        ],
                        [
                            'propertyPath' => 'firstName',
                            'message' => 'Cette valeur ne doit pas être vide.',
                        ],
                        [
                            'propertyPath' => 'lastName',
                            'message' => 'Cette valeur ne doit pas être vide.',
                        ],
                        [
                            'propertyPath' => 'phoneNumber',
                            'message' => 'Cette valeur ne doit pas être vide.',
                        ],
                        [
                            'propertyPath' => 'plainPassword',
                            'message' => 'La force du mot de passe doit être au minimum "Bon".',
                        ],
                        [
                            'propertyPath' => 'company.name',
                            'message' => 'Cette valeur ne doit pas être vide.',
                        ],
                        [
                            'propertyPath' => 'company.billingAddress.countryCode',
                            'message' => "Cette valeur doit être l'un des choix proposés.",
                        ],
                        [
                            'propertyPath' => 'termsOfService',
                            'message' => "Les conditions générales d'utilisations doivent être acceptés.",
                        ],
                    ],
                ],
            ],
            'email_invalid' => [
                [
                    'email' => 'dustin.henderson',
                    'firstName' => 'Dustin',
                    'lastName' => 'Henderson',
                    'phoneNumber' => '+33601020304',
                    'company' => [
                        'name' => 'Stranger Things',
                        'businessActivity' => '/company_business_activities/1',
                        'billingAddress' => [
                            'countryCode' => 'FR',
                        ],
                        'registrationNumber' => '750298820',
                    ],
                    'job' => 'Recruiter IT',
                    'termsOfService' => true,
                    'plainPassword' => '1P@ssw0rd!',
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'email',
                            'message' => "Cette valeur n'est pas une adresse email valide.",
                        ],
                    ],
                ],
            ],
            'email_already_used' => [
                [
                    'email' => 'walter.white@breaking-bad.com',
                    'firstName' => 'Dustin',
                    'lastName' => 'Henderson',
                    'phoneNumber' => '+33601020304',
                    'company' => [
                        'name' => 'Stranger Things',
                        'businessActivity' => '/company_business_activities/1',
                        'billingAddress' => [
                            'countryCode' => 'FR',
                        ],
                        'registrationNumber' => '75029882000028',
                    ],
                    'job' => 'Recruiter IT',
                    'termsOfService' => true,
                    'plainPassword' => '1P@ssw0rd!',
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'email',
                            'message' => 'Cette valeur est déjà utilisée.',
                        ],
                    ],
                ],
            ],
            'company_registration_number_invalid' => [
                [
                    'email' => 'dustin.henderson@stranger-things.com',
                    'firstName' => 'Dustin',
                    'lastName' => 'Henderson',
                    'phoneNumber' => '+33601020304',
                    'company' => [
                        'name' => 'Stranger Things',
                        'businessActivity' => '/company_business_activities/1',
                        'billingAddress' => [
                            'countryCode' => 'FR',
                        ],
                        'registrationNumber' => '12345678910111',
                    ],
                    'job' => 'Recruiter IT',
                    'termsOfService' => true,
                    'plainPassword' => '1P@ssw0rd!',
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'company.registrationNumber',
                            'message' => 'Le numéro de Siren 12345678910111 est invalide.',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideInvalidCases
     */
    public function testInvalidCases(array $payload, array $expected): void
    {
        $client = static::createTurnoverClient();
        $client->request('POST', '/recruiters', [
            'json' => $payload,
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertJsonContains($expected);
    }
}
