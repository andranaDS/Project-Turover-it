<?php

namespace App\User\Tests\Functional\User;

use App\Tests\Functional\ApiTestCase;

class UsersPostIntercontractTest extends ApiTestCase
{
    public static function provideWithValidDataCases(): iterable
    {
        return [
            [
                [
                    'availability' => 'immediate',
                    'visible' => true,
                    'profileJobTitle' => 'Titre CV',
                    'reference' => 'Référence',
                    'jobs' => [
                        ['job' => '/jobs/administrateur-de-base-de-donnee-oracle-sybase'],
                    ],
                    'locationKeys' => [
                        'fr~hauts-de-france~~le-touquet-paris-plage',
                        'fr~auvergne-rhone-alpes~seyssinet-pariset',
                    ],
                    'experienceYear' => '1-2_years',
                    'diplomaLevel' => 2,
                    'averageDailyRate' => 200,
                    'grossAnnualSalary' => 20000,
                    'freelanceCurrency' => 'EUR',
                    'skills' => [
                        ['skill' => '/skills/1'],
                        ['skill' => '/skills/2'],
                    ],
                    'softSkills' => [
                        '/soft_skills/1',
                    ],
                    'introduceYourself' => 'Compétence particulière / description du profil',
                    'contact' => 'Coordonnées de la personne à contacter pour ce profil (nom, téléphone, mail et précisions éventuelles)',
                    'languages' => [
                        [
                            'language' => 'en',
                            'languageLevel' => 'limited_professional_skills',
                        ],
                        [
                            'language' => 'ru',
                            'languageLevel' => 'native_or_bilingual',
                        ],
                    ],
                    'contracts' => [
                        'intercontract',
                    ],
                    'documents' => [
                        [
                            'content' => 'Contenu CV',
                        ],
                    ],
                ],
                [
                    '@context' => '/contexts/User',
                    '@type' => 'User',
                    'profileJobTitle' => 'Titre CV',
                    'experienceYear' => '1-2_years',
                    'visible' => true,
                    'availability' => 'immediate',
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideWithValidDataCases
     */
    public function testWithValidData(array $payload, array $expected): void
    {
        $client = static::createTurnoverAuthenticatedClient();

        $client->request('POST', '/users', [
            'headers' => [
                'Content-Type' => 'multipart/form-data',
            ],
            'extra' => [
                'parameters' => $payload,
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains($expected);
    }

    public static function provideWithEmptyOrInvalidDataCases(): iterable
    {
        return [
            [
                [
                    'visible' => true,
                    'profileJobTitle' => 'Titre CV',
                    'reference' => 'Référence',
                    'jobs' => [
                        ['job' => '/jobs/administrateur-de-base-de-donnee-oracle-sybase'],
                    ],
                    'locationKeys' => [
                        'fr~hauts-de-france~~le-touquet-paris-plage',
                        'fr~auvergne-rhone-alpes~seyssinet-pariset',
                    ],
                    'experienceYear' => '1-2_years',
                    'diplomaLevel' => 2,
                    'averageDailyRate' => 200,
                    'grossAnnualSalary' => 20000,
                    'freelanceCurrency' => 'EUR',
                    'skills' => [
                        ['skill' => '/skills/1'],
                        ['skill' => '/skills/2'],
                    ],
                    'softSkills' => [
                        '/soft_skills/1',
                    ],
                    'introduceYourself' => 'Compétence particulière / description du profil',
                    'contact' => 'Coordonnées de la personne à contacter pour ce profil (nom, téléphone, mail et précisions éventuelles)',
                    'languages' => [
                        [
                            'language' => 'en',
                            'languageLevel' => 'limited_professional_skills',
                        ],
                        [
                            'language' => 'ru',
                            'languageLevel' => 'native_or_bilingual',
                        ],
                    ],
                    'contracts' => [
                        'intercontract',
                    ],
                    'documents' => [
                        [
                            'content' => 'Contenu CV',
                        ],
                    ],
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'availability',
                            'message' => 'Cette valeur ne doit pas être nulle.',
                        ],
                    ],
                ],
            ],
            [
                [
                    'availability' => 'immediate',
                    'visible' => true,
                    'reference' => 'Référence',
                    'jobs' => [
                        ['job' => '/jobs/administrateur-de-base-de-donnee-oracle-sybase'],
                    ],
                    'locationKeys' => [
                        'fr~hauts-de-france~~le-touquet-paris-plage',
                        'fr~auvergne-rhone-alpes~seyssinet-pariset',
                    ],
                    'experienceYear' => '1-2_years',
                    'diplomaLevel' => 2,
                    'averageDailyRate' => 200,
                    'grossAnnualSalary' => 20000,
                    'freelanceCurrency' => 'EUR',
                    'skills' => [
                        ['skill' => '/skills/1'],
                        ['skill' => '/skills/2'],
                    ],
                    'softSkills' => [
                        '/soft_skills/1',
                    ],
                    'introduceYourself' => 'Compétence particulière / description du profil',
                    'contact' => 'Coordonnées de la personne à contacter pour ce profil (nom, téléphone, mail et précisions éventuelles)',
                    'languages' => [
                        [
                            'language' => 'en',
                            'languageLevel' => 'limited_professional_skills',
                        ],
                        [
                            'language' => 'ru',
                            'languageLevel' => 'native_or_bilingual',
                        ],
                    ],
                    'contracts' => [
                        'intercontract',
                    ],
                    'documents' => [
                        [
                            'content' => 'Contenu CV',
                        ],
                    ],
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'profileJobTitle',
                            'message' => 'Cette valeur ne doit pas être vide.',
                        ],
                    ],
                ],
            ],
            [
                [
                    'availability' => 'immediate',
                    'visible' => true,
                    'reference' => 'Référence',
                    'profileJobTitle' => 'Titre CV',
                    'locationKeys' => [
                        'fr~hauts-de-france~~le-touquet-paris-plage',
                        'fr~auvergne-rhone-alpes~seyssinet-pariset',
                    ],
                    'experienceYear' => '1-2_years',
                    'diplomaLevel' => 2,
                    'averageDailyRate' => 200,
                    'grossAnnualSalary' => 20000,
                    'freelanceCurrency' => 'EUR',
                    'skills' => [
                        ['skill' => '/skills/1'],
                        ['skill' => '/skills/2'],
                    ],
                    'softSkills' => [
                        '/soft_skills/1',
                    ],
                    'introduceYourself' => 'Compétence particulière / description du profil',
                    'contact' => 'Coordonnées de la personne à contacter pour ce profil (nom, téléphone, mail et précisions éventuelles)',
                    'languages' => [
                        [
                            'language' => 'en',
                            'languageLevel' => 'limited_professional_skills',
                        ],
                        [
                            'language' => 'ru',
                            'languageLevel' => 'native_or_bilingual',
                        ],
                    ],
                    'contracts' => [
                        'intercontract',
                    ],
                    'documents' => [
                        [
                            'content' => 'Contenu CV',
                        ],
                    ],
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'jobs',
                            'message' => 'Veuillez choisir les éléments obligatoirement dans la liste proposée.',
                        ],
                    ],
                ],
            ],
            [
                [
                    'availability' => 'immediate',
                    'visible' => true,
                    'reference' => 'Référence',
                    'profileJobTitle' => 'Titre CV',
                    'jobs' => [
                        ['job' => '/jobs/administrateur-de-base-de-donnee-oracle-sybase'],
                    ],
                    'experienceYear' => '1-2_years',
                    'diplomaLevel' => 2,
                    'averageDailyRate' => 200,
                    'grossAnnualSalary' => 20000,
                    'freelanceCurrency' => 'EUR',
                    'skills' => [
                        ['skill' => '/skills/1'],
                        ['skill' => '/skills/2'],
                    ],
                    'softSkills' => [
                        '/soft_skills/1',
                    ],
                    'introduceYourself' => 'Compétence particulière / description du profil',
                    'contact' => 'Coordonnées de la personne à contacter pour ce profil (nom, téléphone, mail et précisions éventuelles)',
                    'languages' => [
                        [
                            'language' => 'en',
                            'languageLevel' => 'limited_professional_skills',
                        ],
                        [
                            'language' => 'ru',
                            'languageLevel' => 'native_or_bilingual',
                        ],
                    ],
                    'contracts' => [
                        'intercontract',
                    ],
                    'documents' => [
                        [
                            'content' => 'Contenu CV',
                        ],
                    ],
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'locations',
                            'message' => 'Veuillez choisir les éléments obligatoirement dans la liste proposée.',
                        ],
                    ],
                ],
            ],
            [
                [
                    'availability' => 'immediate',
                    'visible' => true,
                    'reference' => 'Référence',
                    'profileJobTitle' => 'Titre CV',
                    'jobs' => [
                        ['job' => '/jobs/administrateur-de-base-de-donnee-oracle-sybase'],
                    ],
                    'locationKeys' => [
                        'fr~hauts-de-france~~le-touquet-paris-plage',
                        'fr~auvergne-rhone-alpes~seyssinet-pariset',
                    ],
                    'diplomaLevel' => 2,
                    'averageDailyRate' => 200,
                    'grossAnnualSalary' => 20000,
                    'freelanceCurrency' => 'EUR',
                    'skills' => [
                        ['skill' => '/skills/1'],
                        ['skill' => '/skills/2'],
                    ],
                    'softSkills' => [
                        '/soft_skills/1',
                    ],
                    'introduceYourself' => 'Compétence particulière / description du profil',
                    'contact' => 'Coordonnées de la personne à contacter pour ce profil (nom, téléphone, mail et précisions éventuelles)',
                    'languages' => [
                        [
                            'language' => 'en',
                            'languageLevel' => 'limited_professional_skills',
                        ],
                        [
                            'language' => 'ru',
                            'languageLevel' => 'native_or_bilingual',
                        ],
                    ],
                    'contracts' => [
                        'intercontract',
                    ],
                    'documents' => [
                        [
                            'content' => 'Contenu CV',
                        ],
                    ],
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'experienceYear',
                            'message' => 'Cette valeur ne doit pas être vide.',
                        ],
                    ],
                ],
            ],
            [
                [
                    'availability' => 'immediate',
                    'visible' => true,
                    'reference' => 'Référence',
                    'profileJobTitle' => 'Titre CV',
                    'jobs' => [
                        ['job' => '/jobs/administrateur-de-base-de-donnee-oracle-sybase'],
                    ],
                    'locationKeys' => [
                        'fr~hauts-de-france~~le-touquet-paris-plage',
                        'fr~auvergne-rhone-alpes~seyssinet-pariset',
                    ],
                    'experienceYear' => '1-2_years',
                    'diplomaLevel' => 2,
                    'averageDailyRate' => 200,
                    'grossAnnualSalary' => 20000,
                    'freelanceCurrency' => 'EUR',
                    'softSkills' => [
                        '/soft_skills/1',
                    ],
                    'introduceYourself' => 'Compétence particulière / description du profil',
                    'contact' => 'Coordonnées de la personne à contacter pour ce profil (nom, téléphone, mail et précisions éventuelles)',
                    'languages' => [
                        [
                            'language' => 'en',
                            'languageLevel' => 'limited_professional_skills',
                        ],
                        [
                            'language' => 'ru',
                            'languageLevel' => 'native_or_bilingual',
                        ],
                    ],
                    'contracts' => [
                        'intercontract',
                    ],
                    'documents' => [
                        [
                            'content' => 'Contenu CV',
                        ],
                    ],
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'skills',
                            'message' => 'Ce champ doit contenir 1 élément ou plus.',
                        ],
                    ],
                ],
            ],
            [
                [
                    'availability' => 'immediate',
                    'visible' => true,
                    'reference' => 'Référence',
                    'profileJobTitle' => 'Titre CV',
                    'jobs' => [
                        ['job' => '/jobs/administrateur-de-base-de-donnee-oracle-sybase'],
                    ],
                    'locationKeys' => [
                        'fr~hauts-de-france~~le-touquet-paris-plage',
                        'fr~auvergne-rhone-alpes~seyssinet-pariset',
                    ],
                    'experienceYear' => '1-2_years',
                    'diplomaLevel' => 2,
                    'averageDailyRate' => 200,
                    'grossAnnualSalary' => 20000,
                    'freelanceCurrency' => 'EUR',
                    'skills' => [
                        ['skill' => '/skills/1'],
                        ['skill' => '/skills/2'],
                    ],
                    'introduceYourself' => 'Compétence particulière / description du profil',
                    'contact' => 'Coordonnées de la personne à contacter pour ce profil (nom, téléphone, mail et précisions éventuelles)',
                    'languages' => [
                        [
                            'language' => 'en',
                            'languageLevel' => 'limited_professional_skills',
                        ],
                        [
                            'language' => 'ru',
                            'languageLevel' => 'native_or_bilingual',
                        ],
                    ],
                    'contracts' => [
                        'intercontract',
                    ],
                    'documents' => [
                        [
                            'content' => 'Contenu CV',
                        ],
                    ],
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'softSkills',
                            'message' => 'Ce champ doit contenir 1 élément ou plus.',
                        ],
                    ],
                ],
            ],
            [
                [
                    'availability' => 'immediate',
                    'visible' => true,
                    'reference' => 'Référence',
                    'profileJobTitle' => 'Titre CV',
                    'jobs' => [
                        ['job' => '/jobs/administrateur-de-base-de-donnee-oracle-sybase'],
                    ],
                    'locationKeys' => [
                        'fr~hauts-de-france~~le-touquet-paris-plage',
                        'fr~auvergne-rhone-alpes~seyssinet-pariset',
                    ],
                    'experienceYear' => '1-2_years',
                    'diplomaLevel' => 2,
                    'averageDailyRate' => 200,
                    'grossAnnualSalary' => 20000,
                    'freelanceCurrency' => 'EUR',
                    'skills' => [
                        ['skill' => '/skills/1'],
                        ['skill' => '/skills/2'],
                    ],
                    'softSkills' => [
                        '/soft_skills/1',
                    ],
                    'introduceYourself' => 'Compétence particulière / description du profil',
                    'contact' => 'Coordonnées de la personne à contacter pour ce profil (nom, téléphone, mail et précisions éventuelles)',
                    'languages' => [
                        [
                            'language' => 'en',
                            'languageLevel' => 'limited_professional_skills',
                        ],
                        [
                            'language' => 'ru',
                            'languageLevel' => 'native_or_bilingual',
                        ],
                    ],
                    'contracts' => [
                        'intercontract',
                    ],
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'documents',
                            'message' => 'Cette collection doit contenir exactement 1 élément.',
                        ],
                    ],
                ],
            ],
            [
                [
                    'availability' => 'immediate',
                    'visible' => true,
                    'reference' => 'Référence',
                    'profileJobTitle' => 'Titre CV',
                    'jobs' => [
                        ['job' => '/jobs/administrateur-de-base-de-donnee-oracle-sybase'],
                    ],
                    'locationKeys' => [
                        'fr~hauts-de-france~~le-touquet-paris-plage',
                        'fr~auvergne-rhone-alpes~seyssinet-pariset',
                    ],
                    'experienceYear' => '1-2_years',
                    'diplomaLevel' => 2,
                    'averageDailyRate' => 200,
                    'grossAnnualSalary' => 20000,
                    'freelanceCurrency' => 'EUR',
                    'skills' => [
                        ['skill' => '/skills/1'],
                        ['skill' => '/skills/2'],
                    ],
                    'softSkills' => [
                        '/soft_skills/1',
                    ],
                    'introduceYourself' => 'Compétence particulière / description du profil',
                    'contact' => 'Coordonnées de la personne à contacter pour ce profil (nom, téléphone, mail et précisions éventuelles)',
                    'languages' => [
                        [
                            'language' => 'en',
                            'languageLevel' => 'limited_professional_skills',
                        ],
                        [
                            'language' => 'ru',
                            'languageLevel' => 'native_or_bilingual',
                        ],
                    ],
                    'contracts' => [
                        'intercontract',
                    ],
                    'documents' => [
                        [
                            'content' => 'Contenu CV 1',
                        ],
                        [
                            'content' => 'Contenu CV 2',
                        ],
                    ],
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'documents',
                            'message' => 'Cette collection doit contenir exactement 1 élément.',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideWithEmptyOrInvalidDataCases
     */
    public function testWithEmptyOrInvalidData(array $payload, array $expected): void
    {
        $client = static::createTurnoverAuthenticatedClient();

        $client->request('POST', '/users', [
            'headers' => [
                'Content-Type' => 'multipart/form-data',
            ],
            'extra' => [
                'parameters' => $payload,
            ],
        ]);

        self::assertJsonContains($expected);

        self::assertResponseStatusCodeSame(422);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }
}
