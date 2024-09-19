<?php

namespace App\Recruiter\Tests\Functional\Turnover\Recruiter;

use App\Tests\Functional\ApiTestCase;

class GetItemTest extends ApiTestCase
{
    public function testNotLoggedCases(): void
    {
        $client = static::createTurnoverClient();
        $client->request('GET', '/recruiters/2');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedOnOtherRecruiter(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('GET', '/recruiters/2');

        self::assertResponseStatusCodeSame(403);
    }

    public static function provideValidCases(): iterable
    {
        return [
            ['/recruiters/me'],
            ['/recruiters/1'],
        ];
    }

    /**
     * @dataProvider provideValidCases
     */
    public function testValidCases(string $path): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('GET', $path);

        self::assertResponseStatusCodeSame(200);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/Recruiter',
            '@id' => '/recruiters/1',
            '@type' => 'Recruiter',
            'email' => 'walter.white@breaking-bad.com',
            'username' => 'walter.white',
            'firstName' => 'Walter',
            'lastName' => 'White',
            'phoneNumber' => '+33612345678',
            'enabled' => true,
            'company' => [
                '@type' => 'Company',
                'id' => 1,
                'name' => 'Company 1',
                'legalName' => 'Company 1',
                'slug' => 'company-1',
                'businessActivity' => '/company_business_activities/1',
                'registrationNumber' => null,
                'intracommunityVat' => '123456789',
                'billingAddress' => [
                    'street' => '8 Avenue Foch',
                    'locality' => 'Paris',
                    'postalCode' => '75006',
                    'countryCode' => 'FR',
                    'additionalData' => null,
                ],
            ],
            'main' => true,
            'job' => 'CTO',
            'termsOfService' => true,
            'gender' => 'male',
            'passwordUpdateRequired' => true,
            'notification' => [
                'newApplicationEmail' => true,
                'newApplicationNotification' => true,
                'endBroadcastJobPostingEmail' => true,
                'endBroadcastJobPostingNotification' => true,
                'dailyResumeEmail' => true,
                'dailyJobPostingEmail' => true,
                'jobPostingPublishATSEmail' => true,
                'jobPostingPublishATSNotification' => true,
                'newsletterEmail' => true,
                'subscriptionEndEmail' => true,
                'subscriptionEndNotification' => true,
                'invoiceEmail' => true,
                'invoiceNotification' => true,
                'orderEmail' => true,
                'orderNotification' => true,
                'subscriptionEndReminderEmail' => true,
                'subscriptionEndReminderNotification' => true,
            ],
        ]);
    }
}
