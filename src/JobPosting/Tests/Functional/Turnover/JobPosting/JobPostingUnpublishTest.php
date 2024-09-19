<?php

namespace App\JobPosting\Tests\Functional\Turnover\JobPosting;

use App\Tests\Functional\ApiTestCase;

class JobPostingUnpublishTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createTurnoverClient();
        $client->request('PATCH', '/job_postings/1/unpublish');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsNotOwner(): void
    {
        $client = static::createTurnoverAuthenticatedClient('eddard.stark@got.com');
        $client->request('PATCH', '/job_postings/1/unpublish');

        self::assertResponseStatusCodeSame(403);
    }

    public function testNotFound(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('PATCH', '/job_postings/101/unpublish');
        self::assertResponseStatusCodeSame(404);
        self::assertJsonContains(
            [
                '@context' => '/contexts/Error',
                '@type' => 'hydra:Error',
                'hydra:title' => 'An error occurred',
                'hydra:description' => 'Not Found',
            ]
        );
    }

    public function testLoggedAsOwner(): void
    {
        $client = static::createTurnoverAuthenticatedClient();

        $client->request('PATCH', '/job_postings/1/unpublish');
        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(200);
        self::assertJsonContains([
            '@context' => '/contexts/JobPosting',
            '@type' => 'JobPosting',
            'id' => 1,
            'title' => 'Responsable applicatifs Finance (H/F) (CDI)',
            'slug' => 'responsable-applicatifs-finance-h-f-cdi',
            'description' => "Fed IT, cabinet entièrement dédié aux recrutements des métiers de l'IT, recherche pour un établissement parisien en finance de marché un ou une Responsable Support applicatifs Finance (H/F) (CDI)\nAu sein de la direction des Systèmes d'information, votre mission principale sera de maintenir efficacement le fonctionnement des applications métier et de coordonner les équipes support. A ce titre ; \n\n-\tVous assurez le support applicatif auprès des utilisateurs (N1 / N2 / N3). \n-\tVous réalisez les opérations de maintenance ou d'évolution en fonction des contraintes imposées par les marchés financiers. \n-\tVous êtes l'interlocuteur (rice) central(e) entre les chefs de projet, les équipes IT, les équipes de test et les utilisateurs. \n-\tVous participez aux astreintes selon les rotations prévues pour l'équipe. \n-\tVous assurez la gestion et la coordination de l'équipe support IT\nVous possédez à minima 5 ans d'expérience professionnelle sur un poste de support applicatif en environnement financier. \n\nVous avez obligatoirement des connaissances UNIX et SQL. \nVous avez un anglais courant / bilingue car l'intégralité des documents sont rédigés en anglais, certains utilisateurs sont uniquement anglophones. \n\nPoste basé à Paris (75002). \nRémunération : 50-70K fixe en fonction du profil et de l'expérience.",
            'candidateProfile' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent vel commodo dui. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc velit diam, gravida ut auctor et, laoreet eget nunc. Etiam purus risus, auctor ac nisi euismod, condimentum scelerisque ex. Integer aliquet hendrerit velit, non pellentesque nibh interdum quis. Etiam fermentum mi at ex aliquet, ut pretium est posuere. Pellentesque id lectus elit. Duis libero lectus, bibendum non nisi et, ultrices placerat lacus. Nulla vulputate mattis lorem, et aliquet justo vehicula ut. Maecenas varius molestie venenatis.',
            'companyDescription' => 'Proin et feugiat nisi. Quisque vel augue nibh. Maecenas tortor lacus, tempor cursus pretium sollicitudin, dignissim eu ex. Integer suscipit varius mollis. Quisque tincidunt velit at suscipit feugiat. Pellentesque tempor mi nec ex porttitor commodo. Integer quis imperdiet arcu. Cras quis pretium ligula, in interdum sem. Nam a aliquam ligula. Praesent rutrum neque ac sapien tempus mollis. Nulla sagittis mi sem, quis laoreet augue bibendum a. Donec euismod leo quis tempor facilisis. Interdum et malesuada fames ac ante ipsum primis in faucibus. Nam sit amet augue sapien. Praesent volutpat sapien ante, eu euismod lacus tempus vitae. Sed est lorem, aliquet at malesuada eu, consectetur quis elit.',
            'experienceLevel' => 'junior',
            'minAnnualSalary' => 40000,
            'maxAnnualSalary' => 40000,
            'minDailySalary' => null,
            'maxDailySalary' => null,
            'currency' => 'GBP',
            'contracts' => ['permanent'],
            'duration' => 24,
            'renewable' => false,
            'remoteMode' => 'none',
            'applicationType' => 'turnover',
            'applicationContact' => null,
            'applicationUrl' => null,
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
            'company' => [
                '@id' => '/companies/company-1',
                '@type' => 'Company',
                'id' => 1,
                'name' => 'Company 1',
                'description' => 'Company 1 // Description',
                'logo' => [
                    'small' => 'https://api.freework.localhost/media/cache/resolve/company_logo_small/company-1-logo.jpg',
                    'medium' => 'https://api.freework.localhost/media/cache/resolve/company_logo_medium/company-1-logo.jpg',
                ],
            ],
            'startsAt' => null,
            'publishedAt' => '2020-12-07T02:07:40+01:00',
            'annualSalary' => "40k\u{a0}£GB",
            'dailySalary' => null,
            'status' => 'inactive',
            'job' => [
                'id' => 150,
                'name' => "Responsable d'Applications Techniques",
                'slug' => 'responsable-dapplications-techniques',
            ],
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
