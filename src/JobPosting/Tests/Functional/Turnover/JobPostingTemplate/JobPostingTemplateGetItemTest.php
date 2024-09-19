<?php

namespace App\JobPosting\Tests\Functional\Turnover\JobPostingTemplate;

use App\Tests\Functional\ApiTestCase;

class JobPostingTemplateGetItemTest extends ApiTestCase
{
    public function testGetItem(): void
    {
        $client = static::createTurnoverAuthenticatedClient('eddard.stark@got.com');
        $client->request('GET', '/job_posting_templates/1');

        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            '@context' => '/contexts/JobPostingTemplate',
            '@id' => '/job_posting_templates/1',
            '@type' => 'JobPostingTemplate',
            'id' => 1,
            'createdBy' => [
                '@id' => '/recruiters/4',
                '@type' => 'Recruiter',
                'id' => 4,
                'email' => 'eddard.stark@got.com',
                'firstName' => 'Eddard',
                'lastName' => 'Stark',
                'company' => '/companies/company-2',
            ],
            'title' => 'Développeur Web',
            'contracts' => [
                0 => 'permanent',
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
            ],
            'minAnnualSalary' => 45000,
            'maxAnnualSalary' => 55000,
            'minDailySalary' => null,
            'maxDailySalary' => null,
            'currency' => 'EUR',
            'durationValue' => 1,
            'durationPeriod' => 'day',
            'skills' => [
                [
                    '@id' => '/skills/1',
                    '@type' => 'Skill',
                    'id' => 1,
                    'name' => 'php',
                    'slug' => 'php',
                ],
                [
                    '@id' => '/skills/3',
                    '@type' => 'Skill',
                    'id' => 3,
                    'name' => 'javascript',
                    'slug' => 'javascript',
                ],
            ],
            'softSkills' => [
                [
                    '@id' => '/soft_skills/1',
                    '@type' => 'SoftSkill',
                    'id' => 1,
                    'name' => 'SoftSkill 1',
                    'slug' => 'softskill-1',
                ],
                [
                    '@id' => '/soft_skills/2',
                    '@type' => 'SoftSkill',
                    'id' => 2,
                    'name' => 'SoftSkill 2',
                    'slug' => 'softskill-2',
                ],
            ],
            'applicationType' => 'turnover',
            'applicationEmail' => null,
            'applicationContact' => null,
            'createdAt' => '2022-01-01T12:00:00+01:00',
            'updatedAt' => '2022-01-01T12:00:00+01:00',
            'dailySalary' => null,
            'annualSalary' => "45k\u{a0}€",
        ]);
    }

    public function testGetItemNotBelongToTheSameCompany(): void
    {
        $client = static::createTurnoverAuthenticatedClient('gustavo.fring@breaking-bad.com');

        $client->request('GET', '/job_posting_templates/1');
        self::assertResponseStatusCodeSame(403);
        self::assertJsonContains(
            [
                '@context' => '/contexts/Error',
                '@type' => 'hydra:Error',
                'hydra:title' => 'An error occurred',
                'hydra:description' => 'Access Denied.',
            ]
        );
    }

    public function testItemNotFound(): void
    {
        $client = static::createTurnoverAuthenticatedClient('eddard.stark@got.com');
        $client->request('GET', '/job_posting_templates/not-fount');
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

    public function testNotLogged(): void
    {
        $client = static::createTurnoverClient();
        $client->request('GET', '/job_posting_templates/1');

        self::assertResponseStatusCodeSame(401);
        self::assertJsonContains(
            [
                '@context' => '/contexts/Error',
                '@type' => 'hydra:Error',
                'hydra:title' => 'An error occurred',
                'hydra:description' => 'Full authentication is required to access this resource.',
            ]
        );
    }
}
