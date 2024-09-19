<?php

namespace App\JobPosting\Tests\Functional\FreeWork\JobPosting;

use App\JobPosting\Enum\Contract;
use App\JobPosting\Enum\ExperienceLevel;
use App\JobPosting\Enum\RemoteMode;
use App\Tests\Functional\ApiTestCase;

class JobPostingGetBySlugsTest extends ApiTestCase
{
    public static function provideNotFoundCases(): iterable
    {
        return [
            [
                '/job_postings/wrong-job-slug/responsable-applicatifs-finance-h-f-cdi',
            ],
            [
                '/job_postings/responsable-dapplications-techniques/wrong-job-posting-slug',
            ],
            [
                '/job_postings/wrong-job-slug/wrong-job-posting-slug',
            ],
        ];
    }

    /**
     * @dataProvider provideNotFoundCases
     */
    public function testNotFound(string $request): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', $request);

        self::assertResponseStatusCodeSame(404);
    }

    public function testFound(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/job_postings/responsable-dapplications-techniques/responsable-applicatifs-finance-h-f-cdi');

        self::assertJsonContains([
            '@context' => '/contexts/JobPosting',
            '@id' => '/job_postings/1',
            '@type' => 'JobPosting',
            'id' => 1,
            'title' => 'Responsable applicatifs Finance (H/F) (CDI)',
            'slug' => 'responsable-applicatifs-finance-h-f-cdi',
            'description' => "Fed IT, cabinet entièrement dédié aux recrutements des métiers de l'IT, recherche pour un établissement parisien en finance de marché un ou une Responsable Support applicatifs Finance (H/F) (CDI)\nAu sein de la direction des Systèmes d'information, votre mission principale sera de maintenir efficacement le fonctionnement des applications métier et de coordonner les équipes support. A ce titre ; \n\n-\tVous assurez le support applicatif auprès des utilisateurs (N1 / N2 / N3). \n-\tVous réalisez les opérations de maintenance ou d'évolution en fonction des contraintes imposées par les marchés financiers. \n-\tVous êtes l'interlocuteur (rice) central(e) entre les chefs de projet, les équipes IT, les équipes de test et les utilisateurs. \n-\tVous participez aux astreintes selon les rotations prévues pour l'équipe. \n-\tVous assurez la gestion et la coordination de l'équipe support IT\nVous possédez à minima 5 ans d'expérience professionnelle sur un poste de support applicatif en environnement financier. \n\nVous avez obligatoirement des connaissances UNIX et SQL. \nVous avez un anglais courant / bilingue car l'intégralité des documents sont rédigés en anglais, certains utilisateurs sont uniquement anglophones. \n\nPoste basé à Paris (75002). \nRémunération : 50-70K fixe en fonction du profil et de l'expérience.",
            'experienceLevel' => ExperienceLevel::JUNIOR,
            'minAnnualSalary' => 40000,
            'maxAnnualSalary' => 40000,
            'minDailySalary' => null,
            'maxDailySalary' => null,
            'currency' => 'GBP',
            'contracts' => [Contract::PERMANENT],
            'duration' => 24,
            'remoteMode' => RemoteMode::NONE,
            'job' => [
                'id' => 150,
                'name' => "Responsable d'Applications Techniques",
                'slug' => 'responsable-dapplications-techniques',
            ],
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
            'startsAt' => null,
            'company' => [
                '@id' => '/companies/company-1',
                '@type' => 'Company',
                'id' => 1,
                'name' => 'Company 1',
                'slug' => 'company-1',
                'description' => 'Company 1 // Description',
                'businessActivity' => [
                    '@id' => '/company_business_activities/1',
                    '@type' => 'CompanyBusinessActivity',
                    'id' => 1,
                    'name' => 'Business activity 1',
                    'slug' => 'business-activity-1',
                ],
                'size' => [
                    'value' => 'less_than_20_employees',
                    'label' => '< 20',
                ],
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
                'logo' => [
                    'small' => 'https://api.freework.localhost/media/cache/resolve/company_logo_small/company-1-logo.jpg',
                    'medium' => 'https://api.freework.localhost/media/cache/resolve/company_logo_medium/company-1-logo.jpg',
                ],
                'coverPicture' => [
                    'medium' => 'https://api.freework.localhost/media/cache/resolve/company_picture_image_medium/company-1-picture-1.jpg',
                    'large' => 'https://api.freework.localhost/media/cache/resolve/company_picture_image_large/company-1-picture-1.jpg',
                ],
                'data' => [
                    '@type' => 'CompanyData',
                    'jobPostingsPublishedCount' => 12,
                ],
            ],
            'publishedAt' => '2020-12-07T02:07:40+01:00',
            'annualSalary' => "40k\u{a0}£GB",
            'dailySalary' => null,
            'published' => true,
            'skills' => [
                [
                    '@id' => '/skills/1',
                    '@type' => 'Skill',
                    'id' => 1,
                    'name' => 'php',
                    'slug' => 'php',
                ],
                [
                    '@id' => '/skills/2',
                    '@type' => 'Skill',
                    'id' => 2,
                    'name' => 'java',
                    'slug' => 'java',
                ],
                [
                    '@id' => '/skills/3',
                    '@type' => 'Skill',
                    'id' => 3,
                    'name' => 'javascript',
                    'slug' => 'javascript',
                ],
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }
}
