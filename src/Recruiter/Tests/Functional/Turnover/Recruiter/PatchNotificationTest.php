<?php

namespace App\Recruiter\Tests\Functional\Turnover\Recruiter;

use App\Tests\Functional\ApiTestCase;

class PatchNotificationTest extends ApiTestCase
{
    public static function provideWithValidDataCases(): iterable
    {
        return [
            [
                [
                    'notification' => [
                        'newApplicationEmail' => false,
                        'newApplicationNotification' => false,
                        'endBroadcastJobPostingEmail' => false,
                        'endBroadcastJobPostingNotification' => false,
                        'dailyResumeEmail' => false,
                        'dailyJobPostingEmail' => false,
                        'jobPostingPublishATSEmail' => false,
                        'jobPostingPublishATSNotification' => false,
                        'newsletterEmail' => false,
                        'subscriptionEndEmail' => false,
                        'subscriptionEndNotification' => false,
                        'invoiceEmail' => false,
                        'invoiceNotification' => false,
                        'orderEmail' => false,
                        'orderNotification' => false,
                        'subscriptionEndReminderEmail' => false,
                        'subscriptionEndReminderNotification' => false,
                    ],
                ],
                [
                    'notification' => [
                        'newApplicationEmail' => false,
                        'newApplicationNotification' => false,
                        'endBroadcastJobPostingEmail' => false,
                        'endBroadcastJobPostingNotification' => false,
                        'dailyResumeEmail' => false,
                        'dailyJobPostingEmail' => false,
                        'jobPostingPublishATSEmail' => false,
                        'jobPostingPublishATSNotification' => false,
                        'newsletterEmail' => false,
                        'subscriptionEndEmail' => false,
                        'subscriptionEndNotification' => false,
                        'invoiceEmail' => false,
                        'invoiceNotification' => false,
                        'orderEmail' => false,
                        'orderNotification' => false,
                        'subscriptionEndReminderEmail' => false,
                        'subscriptionEndReminderNotification' => false,
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideWithValidDataCases
     */
    public function testNotLogged(array $payload): void
    {
        $client = static::createTurnoverClient();

        $client->request('PATCH', '/recruiters/1/notifications', [
            'json' => $payload,
        ]);

        self::assertResponseStatusCodeSame(401);

        $client->request('PATCH', '/recruiters/2/notifications', [
            'json' => $payload,
        ]);

        self::assertResponseStatusCodeSame(401);
    }

    /**
     * @dataProvider provideWithValidDataCases
     */
    public function testLoggedOnOtherRecruiter(array $payload): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('PATCH', '/recruiters/2/notifications', [
            'json' => $payload,
        ]);

        self::assertResponseStatusCodeSame(403);
    }

    /**
     * @dataProvider provideWithValidDataCases
     */
    public function testValidCases(array $payload, array $expected): void
    {
        $client = static::createTurnoverAuthenticatedClient(); // id 1
        $client->request('PATCH', '/recruiters/1/notifications', [
            'json' => $payload,
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains($expected);
    }
}
