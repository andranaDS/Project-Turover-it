<?php

namespace App\User\Tests\Functional\UserDocument;

use App\Core\Enum\EmploymentTime;
use App\Tests\Functional\ApiTestCase;
use App\User\Entity\User;
use App\User\Enum\UserProfileStep;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UserDocumentPostTest extends ApiTestCase
{
    public function testValidDataLoggedUser(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');

        $container = $client->getContainer();
        if (null === $container) {
            throw new \RuntimeException('Container is null');
        }

        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine')->getManager();

        // 1 - before - User should
        /** @var User $user */
        $user = $em->getRepository(User::class)->findOneBy([
            'email' => 'claude.monet@free-work.fr',
        ]);

        self::assertNotNull($user);
        self::assertSame($user->getFormStep(), UserProfileStep::ABOUT_ME);
        self::assertSame(\count($user->getDocuments()), 3);
        self::assertSame(\count($user->getSoftSkills()), 2);
        self::assertSame(\count($user->getSkills()), 3);
        self::assertSame(\count($user->getLanguages()), 2);

        // 2 - Upload a document - Profile should be complete with parsed data from document
        $file = new UploadedFile(
            'src/User/DataFixtures/files/user-document-3.pdf',
            'Mon CV.pdf',
            'application/pdf',
        );

        $client->request('POST', '/user_documents', [
            'headers' => ['Content-Type' => 'multipart/form-data'],
            'extra' => [
                'parameters' => [
                    'resume' => true,
                ],
                'files' => [
                    'documentFile' => $file,
                ],
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        // 3 - after
        $user = $em->getRepository(User::class)->findOneBy([
            'email' => 'claude.monet@free-work.fr',
        ]);
        self::assertNotNull($user);
        $newDocument = $user->getDocuments()->last()->getDocument();

        self::assertJsonContains([
            '@context' => '/contexts/User',
            '@id' => '/users/6',
            '@type' => 'User',
            'id' => 6,
            'email' => 'claude.monet@free-work.fr',
            'nickname' => 'Claude-Monet',
            'nicknameSlug' => 'claude-monet',
            'phone' => null,
            'roles' => ['ROLE_USER'],
            'firstName' => 'Claude',
            'lastName' => 'Monet',
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
            'anonymous' => false,
            'experienceYear' => '3-4_years',
            'profileWebsite' => 'https://fr.wikipedia.org/wiki/Claude_Monet',
            'profileLinkedInProfile' => 'https://www.linkedin.com/in/claude-monet',
            'profileProjectWebsite' => 'https://www.association-artistique-monet.fr/',
            'freelanceLegalStatus' => 'self_employed',
            'employmentTime' => EmploymentTime::FULL_TIME,
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
            'companyRegistrationNumberBeingAttributed' => false,
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
            'deleted' => false,
            'location' => [
                '@type' => 'Location',
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
                    '@id' => '/user_documents/1',
                    '@type' => 'UserDocument',
                    'id' => 1,
                    'originalName' => 'user-document-1.docx',
                    'document' => getenv('AMAZON_S3_PREFIX') . '/test/users/documents/document1-cm.docx',
                    'resume' => true,
                    'defaultResume' => true,
                ],
                [
                    '@id' => '/user_documents/2',
                    '@type' => 'UserDocument',
                    'id' => 2,
                    'originalName' => 'user-document-2.docx',
                    'document' => getenv('AMAZON_S3_PREFIX') . '/test/users/documents/document2-cm.docx',
                    'resume' => true,
                    'defaultResume' => false,
                ],
                [
                    '@id' => '/user_documents/3',
                    'id' => 3,
                    '@type' => 'UserDocument',
                    'originalName' => 'user-document-3.pdf',
                    'document' => getenv('AMAZON_S3_PREFIX') . '/test/users/documents/document3-cm.pdf',
                    'resume' => false,
                    'defaultResume' => false,
                ],
                [
                    '@type' => 'UserDocument',
                    'originalName' => 'Mon CV.pdf',
                    'document' => getenv('AMAZON_S3_PREFIX') . '/test/users/documents/' . $newDocument,
                    'resume' => true,
                    'defaultResume' => false,
                ],
            ],
            'locations' => [
                [
                    '@type' => 'UserMobility',
                    'location' => [
                        '@type' => 'Location',
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
                    '@type' => 'UserMobility',
                    'location' => [
                        '@type' => 'Location',
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
                '@type' => 'UserFormation',
                'diplomaTitle' => 'Formation - DiplomaTitle - 1',
                'diplomaLevel' => 15,
                'school' => 'Formation - School - 1',
                'diplomaYear' => 1857,
                'beingObtained' => false,
            ],
            'skills' => [
                [
                    '@type' => 'UserSkill',
                    'skill' => [
                        'name' => 'php',
                        'slug' => 'php',
                    ],
                ],
                [
                    '@type' => 'UserSkill',
                    'skill' => [
                        'name' => 'java',
                        'slug' => 'java',
                    ],
                ],
                [
                    '@type' => 'UserSkill',
                    'skill' => [
                        'name' => 'javascript',
                        'slug' => 'javascript',
                    ],
                ],
            ],
            'languages' => [
                [
                    '@type' => 'UserLanguage',
                    'language' => 'fr',
                    'languageLevel' => 'native_or_bilingual',
                ],
                [
                    '@type' => 'UserLanguage',
                    'language' => 'en',
                    'languageLevel' => 'limited_professional_skills',
                ],
            ],
            'softSkills' => [
                [
                    '@type' => 'SoftSkill',
                    'name' => 'SoftSkill 1',
                ],
                [
                    '@type' => 'SoftSkill',
                    'name' => 'SoftSkill 3',
                ],
            ],
        ]);

        // Check that the uploaded cv is here
        /** @phpstan-ignore-next-line  */
        $response = $client->getResponse()->toArray();
        self::assertNotNull(
            $response['documents'][2]['document']
        );
    }

    public static function provideInvalidDataLoggedUserCases(): iterable
    {
        return [
            'too large' => [
                __DIR__ . '/../../Data/user-document-too-large.docx',
                'user-document-too-large.docx',
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'documentFile',
                            'message' => 'Le fichier est trop volumineux (10.49 MB). Sa taille ne doit pas dépasser 10 MB.',
                        ],
                    ],
                ],
            ],
            'wrong mime type' => [
                __DIR__ . '/../../Data/user-document-wrong-mim-type.jpg',
                'user-document-wrong-mime-type.docx',
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'documentFile',
                            'message' => 'Le type du fichier est invalide ("image/jpeg"). Les types autorisés sont "application/pdf".',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideInvalidDataLoggedUserCases
     */
    public function testInvalidDataLoggedUser(string $documentPath, string $documentName, array $expected): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');

        $file = new UploadedFile(
            $documentPath,
            $documentName,
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        );

        $client->request('POST', '/user_documents', [
            'headers' => ['Content-Type' => 'multipart/form-data'],
            'extra' => [
                'parameters' => [
                    'resume' => false,
                ],
                'files' => [
                    'documentFile' => $file,
                ],
            ],
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains($expected);
    }
}
