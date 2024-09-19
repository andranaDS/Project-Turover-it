<?php

namespace App\User\Tests\Functional\User;

use App\Tests\Functional\ApiTestCase;

class UsersLegacyGetTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/legacy/users');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('GET', '/legacy/users');

        self::assertResponseStatusCodeSame(403);
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('GET', '/legacy/users');

        self::assertResponseStatusCodeSame(403);
    }

    public function testLoggedAsTurnover(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/legacy/users', [
            'headers' => [
                'X-AUTH-TOKEN' => $_ENV['TURNOVER_IT_API_KEY'],
            ],
        ]);

        self::assertResponseStatusCodeSame(200);
        self::assertJsonContains([
            '@context' => '/contexts/User',
            '@id' => '/legacy/users',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/users/1',
                    '@type' => 'User',
                    'id' => 1,
                    'email' => 'user@free-work.fr',
                    'phone' => null,
                    'firstName' => 'User',
                    'lastName' => 'Free-Work',
                    'gender' => 'male',
                    'jobTitle' => null,
                    'website' => null,
                    'profileJobTitle' => 'User',
                    'experienceYear' => null,
                    'visible' => true,
                    'availability' => null,
                    'nextAvailabilityAt' => null,
                    'statusUpdatedAt' => '2020-01-01T11:00:00+01:00',
                    'profileWebsite' => null,
                    'profileLinkedInProfile' => null,
                    'profileProjectWebsite' => null,
                    'freelanceLegalStatus' => null,
                    'employmentTime' => null,
                    'freelanceCurrency' => null,
                    'employeeCurrency' => null,
                    'companyCountryCode' => null,
                    'introduceYourself' => null,
                    'drivingLicense' => false,
                    'employee' => false,
                    'freelance' => false,
                    'fulltimeTeleworking' => false,
                    'companyRegistrationNumberBeingAttributed' => false,
                    'profileCompleted' => false,
                    'anonymous' => false,
                    'grossAnnualSalary' => null,
                    'averageDailyRate' => null,
                    'companyRegistrationNumber' => null,
                    'contracts' => null,
                    'birthdate' => null,
                    'deletedAt' => null,
                    'location' => [
                        '@type' => 'Location',
                        'street' => null,
                        'locality' => null,
                        'postalCode' => null,
                        'adminLevel1' => null,
                        'adminLevel2' => null,
                        'country' => null,
                        'countryCode' => null,
                        'latitude' => null,
                        'longitude' => null,
                        'label' => null,
                        'shortLabel' => null,
                    ],
                    'documents' => [],
                    'locations' => [],
                    'formation' => null,
                    'skills' => [],
                    'jobs' => [],
                    'softSkills' => [],
                    'blacklistedCompanies' => [],
                    'umbrellaCompany' => null,
                    'oldFreelanceInfoIds' => null,
                    'oldFreelanceInfoProfileIds' => null,
                    'oldCarriereInfoIds' => null,
                    'oldCarriereInfoProfileIds' => null,
                    'createdAtTimestamp' => 1577869200,
                    'updatedAtTimestamp' => 1577872800,
                    'deletedAtTimestamp' => null,
                    'languages' => [],
                ],
                [
                    '@id' => '/users/2',
                    '@type' => 'User',
                    'id' => 2,
                    'email' => 'admin@free-work.fr',
                    'phone' => null,
                    'firstName' => 'Admin',
                    'lastName' => 'Free-Work',
                    'gender' => 'male',
                    'jobTitle' => null,
                    'website' => null,
                    'profileJobTitle' => null,
                    'experienceYear' => null,
                    'visible' => true,
                    'availability' => null,
                    'nextAvailabilityAt' => null,
                    'statusUpdatedAt' => '2020-01-01T11:00:00+01:00',
                    'profileWebsite' => null,
                    'profileLinkedInProfile' => null,
                    'profileProjectWebsite' => null,
                    'freelanceLegalStatus' => null,
                    'employmentTime' => null,
                    'freelanceCurrency' => null,
                    'employeeCurrency' => null,
                    'companyCountryCode' => null,
                    'introduceYourself' => null,
                    'drivingLicense' => false,
                    'employee' => false,
                    'freelance' => false,
                    'fulltimeTeleworking' => false,
                    'companyRegistrationNumberBeingAttributed' => false,
                    'profileCompleted' => false,
                    'anonymous' => false,
                    'grossAnnualSalary' => null,
                    'averageDailyRate' => null,
                    'companyRegistrationNumber' => null,
                    'contracts' => null,
                    'birthdate' => null,
                    'deletedAt' => null,
                    'location' => [
                        '@type' => 'Location',
                        'street' => null,
                        'locality' => null,
                        'postalCode' => null,
                        'adminLevel1' => null,
                        'adminLevel2' => null,
                        'country' => null,
                        'countryCode' => null,
                        'latitude' => null,
                        'longitude' => null,
                        'label' => null,
                        'shortLabel' => null,
                    ],
                    'documents' => [],
                    'locations' => [],
                    'formation' => null,
                    'skills' => [],
                    'jobs' => [],
                    'softSkills' => [],
                    'blacklistedCompanies' => [],
                    'umbrellaCompany' => null,
                    'oldFreelanceInfoIds' => null,
                    'oldFreelanceInfoProfileIds' => null,
                    'oldCarriereInfoIds' => null,
                    'oldCarriereInfoProfileIds' => null,
                    'createdAtTimestamp' => 1577869200,
                    'updatedAtTimestamp' => 1577872800,
                    'deletedAtTimestamp' => null,
                    'languages' => [],
                ],
            ],
            'hydra:view' => [
                '@id' => '/legacy/users?page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:next' => '/legacy/users?page=2',
            ],
            'hydra:search' => [
                '@type' => 'hydra:IriTemplate',
                'hydra:template' => '/legacy/users{?createdAt[before],createdAt[strictly_before],createdAt[after],createdAt[strictly_after],updatedAt[before],updatedAt[strictly_before],updatedAt[after],updatedAt[strictly_after],deletedAt[before],deletedAt[strictly_before],deletedAt[after],deletedAt[strictly_after],visible,applicationsCount[between],applicationsCount[gt],applicationsCount[gte],applicationsCount[lt],applicationsCount[lte],applicationsCount,applicationsCount[],order[applicationsCount],order[viewsCount]}',
                'hydra:variableRepresentation' => 'BasicRepresentation',
                'hydra:mapping' => [
                    [
                        '@type' => 'IriTemplateMapping',
                        'variable' => 'createdAt[before]',
                        'property' => 'createdAt',
                        'required' => false,
                    ],
                    [
                        '@type' => 'IriTemplateMapping',
                        'variable' => 'createdAt[strictly_before]',
                        'property' => 'createdAt',
                        'required' => false,
                    ],
                    [
                        '@type' => 'IriTemplateMapping',
                        'variable' => 'createdAt[after]',
                        'property' => 'createdAt',
                        'required' => false,
                    ],
                    [
                        '@type' => 'IriTemplateMapping',
                        'variable' => 'createdAt[strictly_after]',
                        'property' => 'createdAt',
                        'required' => false,
                    ],
                    [
                        '@type' => 'IriTemplateMapping',
                        'variable' => 'updatedAt[before]',
                        'property' => 'updatedAt',
                        'required' => false,
                    ],
                    [
                        '@type' => 'IriTemplateMapping',
                        'variable' => 'updatedAt[strictly_before]',
                        'property' => 'updatedAt',
                        'required' => false,
                    ],
                    [
                        '@type' => 'IriTemplateMapping',
                        'variable' => 'updatedAt[after]',
                        'property' => 'updatedAt',
                        'required' => false,
                    ],
                    [
                        '@type' => 'IriTemplateMapping',
                        'variable' => 'updatedAt[strictly_after]',
                        'property' => 'updatedAt',
                        'required' => false,
                    ],
                    [
                        '@type' => 'IriTemplateMapping',
                        'variable' => 'deletedAt[before]',
                        'property' => 'deletedAt',
                        'required' => false,
                    ],
                    [
                        '@type' => 'IriTemplateMapping',
                        'variable' => 'deletedAt[strictly_before]',
                        'property' => 'deletedAt',
                        'required' => false,
                    ],
                    [
                        '@type' => 'IriTemplateMapping',
                        'variable' => 'deletedAt[after]',
                        'property' => 'deletedAt',
                        'required' => false,
                    ],
                    [
                        '@type' => 'IriTemplateMapping',
                        'variable' => 'deletedAt[strictly_after]',
                        'property' => 'deletedAt',
                        'required' => false,
                    ],
                ],
            ],
        ]);
    }
}
