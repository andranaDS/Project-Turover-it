<?php

namespace App\User\Tests\Functional\User;

use App\Tests\Functional\ApiTestCase;

class UserMeGetTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/users/me');

        self::assertResponseStatusCodeSame(404);
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('GET', '/users/me');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('GET', '/users/me');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public static function provideMandatoryDataCases(): iterable
    {
        return [
            [
                [
                    '@context' => '/contexts/User',
                    '@id' => '/users/6',
                    '@type' => 'User',
                    'id' => 6,
                    'email' => 'claude.monet@free-work.fr',
                    'enabled' => true,
                    'nickname' => 'Claude-Monet',
                    'nicknameSlug' => 'claude-monet',
                    'hasPassword' => true,
                    'phone' => null,
                    'roles' => [
                        'ROLE_USER',
                    ],
                    'firstName' => 'Claude',
                    'lastName' => 'Monet',
                    'termsOfService' => true,
                    'gender' => 'male',
                    'notification' => [
                        'marketingNewsletter' => true,
                        'forumTopicReply' => true,
                        'forumTopicFavorite' => true,
                        'forumPostReply' => true,
                        'forumPostLike' => true,
                        'messagingNewMessage' => true,
                    ],
                    'jobTitle' => 'Peintre',
                    'website' => 'https://fr.wikipedia.org/wiki/Claude_Monet',
                    'profileJobTitle' => 'Peintre',
                    'experienceYear' => '3-4_years',
                    'availability' => 'immediate',
                    'profileWebsite' => 'https://fr.wikipedia.org/wiki/Claude_Monet',
                    'profileLinkedInProfile' => 'https://www.linkedin.com/in/claude-monet',
                    'profileProjectWebsite' => 'https://www.association-artistique-monet.fr/',
                    'freelanceLegalStatus' => 'self_employed',
                    'employmentTime' => 'full_time',
                    'formStep' => 'about_me',
                    'freelanceCurrency' => 'EUR',
                    'employeeCurrency' => 'EUR',
                    'companyCountryCode' => 'FR',
                    'introduceYourself' => 'Claude Monnet',
                    'signature' => 'Claude Monet.',
                    'avatar' => [
                        'xSmall' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_x_small/monet-avatar.jpg',
                        'small' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_small/monet-avatar.jpg',
                        'medium' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_medium/monet-avatar.jpg',
                    ],
                    'displayAvatar' => true,
                    'drivingLicense' => true,
                    'employee' => true,
                    'freelance' => true,
                    'fulltimeTeleworking' => true,
                    'companyRegistrationNumberBeingAttributed' => false,
                    'profileCompleted' => true,
                    'anonymous' => false,
                    'grossAnnualSalary' => 40000,
                    'averageDailyRate' => 300,
                    'companyRegistrationNumber' => '123456789',
                    'contracts' => [
                        'fixed-term',
                        'permanent',
                    ],
                    'forumPostUpvotesCount' => 2,
                    'forumPostsCount' => 7,
                    'birthdate' => '1840-11-14T00:00:00+00:09',
                    'createdAt' => '2020-01-01T10:00:00+01:00',
                    'updatedAt' => '2020-01-01T11:00:00+01:00',
                    'deleted' => false,
                    'location' => [
                        'street' => null,
                        'locality' => 'Paris',
                        'postalCode' => null,
                        'adminLevel1' => 'Île-de-France',
                        'adminLevel2' => null,
                        'country' => 'France',
                        'countryCode' => 'FR',
                        'latitude' => '48.8588897',
                        'longitude' => '2.3200410',
                        'key' => 'fr~ile-de-france~~paris',
                        'label' => 'Paris, Île-de-France',
                        'shortLabel' => 'Paris',
                    ],
                    'documents' => [
                        [
                            'document' => getenv('AMAZON_S3_PREFIX') . '/test/users/documents/document1-cm.docx',
                            'resume' => true,
                            'defaultResume' => true,
                        ],
                        [
                            'document' => getenv('AMAZON_S3_PREFIX') . '/test/users/documents/document2-cm.docx',
                            'resume' => true,
                            'defaultResume' => false,
                        ],
                        [
                            'document' => getenv('AMAZON_S3_PREFIX') . '/test/users/documents/document3-cm.pdf',
                            'resume' => false,
                            'defaultResume' => false,
                        ],
                    ],
                    'locations' => [
                        [
                            'location' => [
                                'street' => null,
                                'locality' => 'Paris',
                                'postalCode' => null,
                                'adminLevel1' => 'Île-de-France',
                                'adminLevel2' => null,
                                'country' => 'France',
                                'countryCode' => 'FR',
                                'latitude' => '48.8588897',
                                'longitude' => '2.3200410',
                                'key' => 'fr~ile-de-france~~paris',
                                'label' => 'Paris, Île-de-France',
                                'shortLabel' => 'Paris',
                            ],
                        ],
                        [
                            'location' => [
                                'street' => null,
                                'locality' => 'Lyon',
                                'postalCode' => null,
                                'adminLevel1' => 'Auvergne-Rhône-Alpes',
                                'adminLevel2' => 'Métropole de Lyon',
                                'country' => 'France',
                                'countryCode' => 'FR',
                                'latitude' => '45.7578137',
                                'longitude' => '4.8320114',
                                'key' => 'fr~auvergne-rhone-alpes~metropole-de-lyon~lyon',
                                'label' => 'Lyon, Auvergne-Rhône-Alpes',
                                'shortLabel' => 'Lyon',
                            ],
                        ],
                    ],
                    'formation' => [
                        'diplomaTitle' => 'Formation - DiplomaTitle - 1',
                        'diplomaLevel' => 15,
                        'school' => 'Formation - School - 1',
                        'diplomaYear' => 1857,
                        'beingObtained' => false,
                    ],
                    'skills' => [
                        [
                            'skill' => [
                                'name' => 'php',
                                'slug' => 'php',
                            ],
                            'main' => true,
                        ],
                        [
                            'skill' => [
                                'name' => 'java',
                                'slug' => 'java',
                            ],
                            'main' => true,
                        ],
                        [
                            'skill' => [
                                'name' => 'javascript',
                                'slug' => 'javascript',
                            ],
                            'main' => false,
                        ],
                    ],
                    'languages' => [
                        [
                            'language' => 'fr',
                            'languageLevel' => 'native_or_bilingual',
                        ],
                        [
                            'language' => 'en',
                            'languageLevel' => 'limited_professional_skills',
                        ],
                    ],
                    'jobs' => [
                        [
                            'job' => [
                                'name' => 'Analyste réalisateur',
                                'slug' => 'analyste-realisateur',
                            ],
                            'main' => true,
                        ],
                        [
                            'job' => [
                                'name' => 'Développeur',
                                'slug' => 'developpeur',
                            ],
                            'main' => true,
                        ],
                    ],
                    'softSkills' => [
                        [
                            'name' => 'SoftSkill 1',
                            'slug' => 'softskill-1',
                        ],
                        [
                            'name' => 'SoftSkill 3',
                            'slug' => 'softskill-3',
                        ],
                    ],
                    'umbrellaCompany' => null,
                    'providers' => [
                        [
                            'email' => 'claude.monet-linkedin@free-work.fr',
                            'provider' => 'linkedin',
                            'updatedAt' => '2020-01-01T11:00:00+01:00',
                        ],
                        [
                            'email' => 'claude.monet-google@free-work.fr',
                            'provider' => 'google',
                            'updatedAt' => '2020-01-01T12:00:00+01:00',
                        ],
                    ],
                    'formattedGrossAnnualSalary' => "40k\u{a0}€",
                    'formattedAverageDailyRate' => "300\u{a0}€",
                    'lastLoginProvider' => 'email',
                    'partner' => [
                        'id' => 4,
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideMandatoryDataCases
     */
    public function testMandatoryData(array $expected): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr', 'P@ssw0rd', true);
        $client->request('GET', '/users/me');

        self::assertJsonContains($expected);

        self::assertResponseIsSuccessful();
    }
}
