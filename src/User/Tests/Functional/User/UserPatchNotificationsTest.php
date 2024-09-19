<?php

namespace App\User\Tests\Functional\User;

use App\Tests\Functional\ApiTestCase;

class UserPatchNotificationsTest extends ApiTestCase
{
    public static function provideWithValidDataCases(): iterable
    {
        return [
            [
                [
                    'notification' => [
                        'marketingNewsletter' => false,
                        'forumTopicReply' => false,
                        'forumTopicFavorite' => false,
                        'forumPostReply' => false,
                        'forumPostLike' => false,
                        'messagingNewMessage' => false,
                    ],
                ],
                [
                    'notification' => [
                        'marketingNewsletter' => false,
                        'forumTopicReply' => false,
                        'forumTopicFavorite' => false,
                        'forumPostReply' => false,
                        'forumPostLike' => false,
                        'messagingNewMessage' => false,
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
        $client = static::createFreeWorkClient();

        $client->request('PATCH', '/users/1/notifications', [
            'json' => $payload,
        ]);

        self::assertResponseStatusCodeSame(401);

        $client->request('PATCH', '/users/2/notifications', [
            'json' => $payload,
        ]);

        self::assertResponseStatusCodeSame(401);
    }

    /**
     * @dataProvider provideWithValidDataCases
     */
    public function testLoggedAsUser(array $payload): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('PATCH', '/users/2/notifications', [
            'json' => $payload,
        ]);

        self::assertResponseStatusCodeSame(403);
    }

    /**
     * @dataProvider provideWithValidDataCases
     */
    public function testLoggedAsAdmin(array $payload): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('PATCH', '/users/1/notifications', [
            'json' => $payload,
        ]);

        self::assertResponseStatusCodeSame(403);
    }

    /**
     * @dataProvider provideWithValidDataCases
     */
    public function testWithValidDataOnItsOwnEntityAndLoggedAsUser(array $payload, array $expected): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient(); // id 1
        $client->request('PATCH', '/users/1/notifications', [
            'json' => $payload,
        ]);

        self::assertResponseIsSuccessful();
        self::assertJsonContains($expected);
    }
}
