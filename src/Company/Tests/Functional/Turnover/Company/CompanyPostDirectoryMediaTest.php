<?php

namespace App\Company\Tests\Functional\Turnover\Company;

use App\Recruiter\Entity\Recruiter;
use App\Tests\Functional\ApiTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CompanyPostDirectoryMediaTest extends ApiTestCase
{
    public static function provideValidCases(): iterable
    {
        return [
            'without_data' => [
                [
                    'headers' => ['Content-Type' => 'multipart/form-data'],
                    'extra' => [
                        'files' => [],
                    ],
                ],
                [
                    '@context' => '/contexts/Company',
                    '@id' => '/companies/company-1',
                    '@type' => 'Company',
                    'logo' => [
                        'small' => 'https://api.freework.localhost/media/cache/resolve/company_logo_small/company-1-logo.jpg',
                        'medium' => 'https://api.freework.localhost/media/cache/resolve/company_logo_medium/company-1-logo.jpg',
                    ],
                    'pictures' => [
                        [
                            '@type' => 'CompanyPicture',
                            'id' => 1,
                            'image' => [
                                'medium' => 'https://api.freework.localhost/media/cache/resolve/company_picture_image_medium/company-1-picture-2.jpg',
                                'large' => 'https://api.freework.localhost/media/cache/resolve/company_picture_image_large/company-1-picture-2.jpg',
                            ],
                        ],
                        [
                            '@type' => 'CompanyPicture',
                            'id' => 2,
                            'image' => [
                                'medium' => 'https://api.freework.localhost/media/cache/resolve/company_picture_image_medium/company-1-picture-3.jpg',
                                'large' => 'https://api.freework.localhost/media/cache/resolve/company_picture_image_large/company-1-picture-3.jpg',
                            ],
                        ],
                    ],
                    'coverPicture' => [
                        'medium' => 'https://api.freework.localhost/media/cache/resolve/company_picture_image_medium/company-1-picture-1.jpg',
                        'large' => 'https://api.freework.localhost/media/cache/resolve/company_picture_image_large/company-1-picture-1.jpg',
                    ],
                    'video' => 'https://s1-storage.dev.free-work.com/companies/videos/video.mp4',
                ],
            ],
            'empty_data' => [
                [
                    'headers' => ['Content-Type' => 'multipart/form-data'],
                    'extra' => [
                        'parameters' => [
                            'logoFile' => '',
                            'videoFile' => '',
                            'coverPictureFile' => '',
                            'pictures' => [
                                [
                                    'imageFile' => '',
                                ],
                                [
                                    'imageFile' => '',
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    '@context' => '/contexts/Company',
                    '@id' => '/companies/company-1',
                    '@type' => 'Company',
                    'logo' => null,
                    'pictures' => [],
                    'coverPicture' => null,
                    'video' => null,
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideValidCases
     */
    public function testValidCases(array $payload, array $expected): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('POST', '/companies/company-1/directory_media', $payload);
        self::assertResponseStatusCodeSame(201);
        self::assertJsonContains($expected);
    }

    /**
     * @dataProvider provideValidCases
     */
    public function testNotLoggedCases(array $payload): void
    {
        $client = static::createTurnoverClient();
        $client->request('POST', '/companies/company-1/directory_media', $payload);

        self::assertResponseStatusCodeSame(401);
    }

    /**
     * @dataProvider provideValidCases
     */
    public function testLoggedOnWrongCompany(array $payload): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('POST', '/companies/company-2/directory_media', $payload);

        self::assertResponseStatusCodeSame(403);
    }

    public static function provideValidUploadCases(): iterable
    {
        return [
            ['/companies/company-1/directory_media'],
            ['/companies/mine/directory_media'],
        ];
    }

    /**
     * @dataProvider provideValidUploadCases
     */
    public function testValidUpload(string $path): void
    {
        $client = static::createTurnoverAuthenticatedClient();

        $logoFile = new UploadedFile(
            'src/Company/DataFixtures/files/companies/2-logo.png',
            '2-logo.png',
            'image/png',
        );

        $videoFile = new UploadedFile(
            'src/Company/DataFixtures/files/companies/video.mp4',
            'video-2.mp4',
            'video/mp4',
        );

        $coverPictureFile = new UploadedFile(
            'src/Company/DataFixtures/files/companies/2-picture-1.jpg',
            '2-picture-1.jpg',
            'image/jpg',
        );

        $companyPictureFile1 = new UploadedFile(
            'src/Company/DataFixtures/files/companies/3-picture-1.jpg',
            '3-picture-1.jpg',
            'image/jpg',
        );

        $companyPictureFile2 = new UploadedFile(
            'src/Company/DataFixtures/files/companies/4-picture-1.jpg',
            '4-picture-1.jpg',
            'image/jpg',
        );

        $container = $client->getContainer();
        if (null === $container) {
            throw new \RuntimeException('Container is null');
        }
        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine')->getManager();

        // 1 - before
        $recruiter = $em->getRepository(Recruiter::class)->findOneBy([
            'email' => 'walter.white@breaking-bad.com',
        ]);
        self::assertNotNull($recruiter);
        $company = $recruiter->getCompany();
        self::assertNotNull($company);
        self::assertSame('company-1-logo.jpg', $company->getLogo());
        self::assertSame('company-1-picture-1.jpg', $company->getCoverPicture());
        self::assertSame('video.mp4', $company->getVideo());
        self::assertSame('company-1-picture-2.jpg', $company->getPictures()->get(0)->getImage());
        self::assertSame('company-1-picture-3.jpg', $company->getPictures()->get(1)->getImage());

        // 2 - post file
        $client->request('POST', $path, [
            'headers' => ['Content-Type' => 'multipart/form-data'],
            'extra' => [
                'parameters' => [
                    'pictures' => [
                        1 => '/company_pictures/1',
                    ],
                ],
                'files' => [
                    'logoFile' => $logoFile,
                    'videoFile' => $videoFile,
                    'coverPictureFile' => $coverPictureFile,
                    'pictures' => [
                        0 => [
                            'imageFile' => $companyPictureFile1,
                        ],
                        2 => [
                            'imageFile' => $companyPictureFile2,
                        ],
                    ],
                ],
            ],
        ]);
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        // 3 - after
        $recruiter = $em->getRepository(Recruiter::class)->findOneBy([
            'email' => 'walter.white@breaking-bad.com',
        ]);
        self::assertNotNull($recruiter);
        $company = $recruiter->getCompany();
        self::assertNotNull($company);

        $newLogo = $company->getLogo();
        self::assertNotNull($newLogo);
        self::assertNotSame('company-1-logo.jpg', $newLogo);

        $newCoverPicture = $company->getCoverPicture();
        self::assertNotNull($newCoverPicture);
        self::assertNotSame('company-1-picture-1.jpg', $newCoverPicture);

        $newVideo = $company->getVideo();
        self::assertNotNull($newVideo);
        self::assertNotSame('video.mp4', $newVideo);

        $newCompanyPicture1 = $company->getPictures()->get(0)->getImage();
        $newCompanyPicture2 = $company->getPictures()->get(1)->getImage();
        $newCompanyPicture3 = $company->getPictures()->get(2)->getImage();
        self::assertNotNull($newCompanyPicture1);
        self::assertNotNull($newCompanyPicture2);
        self::assertNotSame('company-1-picture-2.jpg', $newCompanyPicture1);
        self::assertSame('company-1-picture-2.jpg', $newCompanyPicture2);
        self::assertNotSame('company-1-picture-2.jpg', $newCompanyPicture3);

        self::assertJsonContains([
            '@context' => '/contexts/Company',
            '@id' => '/companies/company-1',
            '@type' => 'Company',
            'logo' => [
                'small' => 'https://api.freework.localhost/media/cache/resolve/company_logo_small/' . $newLogo,
                'medium' => 'https://api.freework.localhost/media/cache/resolve/company_logo_medium/' . $newLogo,
            ],
            'pictures' => [
                [
                    '@type' => 'CompanyPicture',
                    'image' => [
                        'medium' => 'https://api.freework.localhost/media/cache/resolve/company_picture_image_medium/' . $newCompanyPicture1,
                        'large' => 'https://api.freework.localhost/media/cache/resolve/company_picture_image_large/' . $newCompanyPicture1,
                    ],
                ],
                [
                    '@id' => '/company_pictures/1',
                    '@type' => 'CompanyPicture',
                    'id' => 1,
                    'image' => [
                        'medium' => 'https://api.freework.localhost/media/cache/resolve/company_picture_image_medium/company-1-picture-2.jpg',
                        'large' => 'https://api.freework.localhost/media/cache/resolve/company_picture_image_large/company-1-picture-2.jpg',
                    ],
                ],
                [
                    '@type' => 'CompanyPicture',
                    'image' => [
                        'medium' => 'https://api.freework.localhost/media/cache/resolve/company_picture_image_medium/' . $newCompanyPicture3,
                        'large' => 'https://api.freework.localhost/media/cache/resolve/company_picture_image_large/' . $newCompanyPicture3,
                    ],
                ],
            ],
            'coverPicture' => [
                'medium' => 'https://api.freework.localhost/media/cache/resolve/company_picture_image_medium/' . $newCoverPicture,
                'large' => 'https://api.freework.localhost/media/cache/resolve/company_picture_image_large/' . $newCoverPicture,
            ],
            'video' => 'https://s1-storage.dev.free-work.com/companies/videos/' . $newVideo,
        ]);
    }

    /**
     * @dataProvider provideValidUploadCases
     */
    public function testValidWithoutUpload(string $path): void
    {
        $client = static::createTurnoverAuthenticatedClient();

        $client->request('POST', $path, [
            'headers' => ['Content-Type' => 'multipart/form-data'],
            'extra' => [
                'parameters' => [
                    'pictures' => [
                        0 => '/company_pictures/2',
                        1 => '/company_pictures/1',
                    ],
                ],
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/Company',
            '@id' => '/companies/company-1',
            '@type' => 'Company',
            'pictures' => [
                [
                    '@id' => '/company_pictures/2',
                    '@type' => 'CompanyPicture',
                    'id' => 2,
                    'image' => [
                        'medium' => 'https://api.freework.localhost/media/cache/resolve/company_picture_image_medium/company-1-picture-3.jpg',
                        'large' => 'https://api.freework.localhost/media/cache/resolve/company_picture_image_large/company-1-picture-3.jpg',
                    ],
                ],
                [
                    '@id' => '/company_pictures/1',
                    '@type' => 'CompanyPicture',
                    'id' => 1,
                    'image' => [
                        'medium' => 'https://api.freework.localhost/media/cache/resolve/company_picture_image_medium/company-1-picture-2.jpg',
                        'large' => 'https://api.freework.localhost/media/cache/resolve/company_picture_image_large/company-1-picture-2.jpg',
                    ],
                ],
            ],
        ]);
    }

    public static function provideInvalidUploadCases(): iterable
    {
        return [
            'too large image' => [
                __DIR__ . '/../../../Data/large-image.jpg',
                'large-image.jpg',
                'image/jpg',
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'logoFile',
                            'message' => 'Le fichier est trop volumineux (19.6 MB). Sa taille ne doit pas dépasser 2 MB.',
                        ],
                        [
                            'propertyPath' => 'pictures[2].imageFile',
                            'message' => 'La largeur de l\'image est trop grande (16384px). La largeur maximale autorisée est de 4096px.',
                        ],
                        [
                            'propertyPath' => 'pictures[3].imageFile',
                            'message' => 'La largeur de l\'image est trop grande (16384px). La largeur maximale autorisée est de 4096px.',
                        ],
                        [
                            'propertyPath' => 'coverPictureFile',
                            'message' => 'La largeur de l\'image est trop grande (16384px). La largeur maximale autorisée est de 4096px.',
                        ],
                        [
                            'propertyPath' => 'videoFile',
                            'message' => 'Le fichier est trop volumineux (19.6 MB). Sa taille ne doit pas dépasser 10 MB.',
                        ],
                    ],
                ],
            ],
            'wrong mime type' => [
                'src/Company/DataFixtures/files/companies/video.mp4',
                'video.mp4',
                'video/mp4',
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'logoFile',
                            'message' => 'Le type du fichier est invalide ("video/mp4"). Les types autorisés sont "image/jpeg", "image/png", "image/gif", "image/jpg".',
                        ],
                        [
                            'propertyPath' => 'pictures[2].imageFile',
                            'message' => 'Le type du fichier est invalide ("video/mp4"). Les types autorisés sont "image/jpeg", "image/png", "image/gif", "image/jpg".',
                        ],
                        [
                            'propertyPath' => 'pictures[3].imageFile',
                            'message' => 'Le type du fichier est invalide ("video/mp4"). Les types autorisés sont "image/jpeg", "image/png", "image/gif", "image/jpg".',
                        ],
                        [
                            'propertyPath' => 'coverPictureFile',
                            'message' => 'Le type du fichier est invalide ("video/mp4"). Les types autorisés sont "image/jpeg", "image/png", "image/gif", "image/jpg".',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideInvalidUploadCases
     */
    public function testInvalidUpload(string $documentPath, string $documentName, string $mimeType, array $expected): void
    {
        $client = static::createTurnoverAuthenticatedClient();

        $logoFile = new UploadedFile($documentPath, $documentName, $mimeType);
        $videoFile = new UploadedFile($documentPath, $documentName, $mimeType);
        $coverPictureFile = new UploadedFile($documentPath, $documentName, $mimeType);
        $companyPictureFile1 = new UploadedFile($documentPath, $documentName, $mimeType);
        $companyPictureFile2 = new UploadedFile($documentPath, $documentName, $mimeType);

        $client->request('POST', '/companies/company-1/directory_media', [
            'headers' => ['Content-Type' => 'multipart/form-data'],
            'extra' => [
                'files' => [
                    'logoFile' => $logoFile,
                    'videoFile' => $videoFile,
                    'coverPictureFile' => $coverPictureFile,
                    'pictures' => [
                        [
                            'imageFile' => $companyPictureFile1,
                        ],
                        [
                            'imageFile' => $companyPictureFile2,
                        ],
                    ],
                ],
            ],
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains($expected);
    }
}
