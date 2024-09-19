<?php

namespace App\User\Tests\Functional\Mailjet;

use App\Tests\Functional\ApiTestCase;
use App\User\Entity\MailjetUnsubscribeLog;
use App\User\Entity\User;

class UnsubscribeTest extends ApiTestCase
{
    public static function provideWithoutValidDataCases(): iterable
    {
        return [
            'without event' => [
                [
                    'email' => 'claude.monet@free-work.fr',
                ],
            ],
            'wrong event' => [
                [
                    'email' => 'claude.monet@free-work.fr',
                    'event' => 'sent',
                ],
            ],
            'without email' => [
                [
                    'event' => 'unsub',
                ],
            ],
            'not found email' => [
                [
                    'event' => 'unsub',
                    'email' => 'not.found@free-work.fr',
                ],
            ],
        ];
    }

    /**
     * @dataProvider  provideWithoutValidDataCases
     */
    public function testWithoutValidData(array $payload): void
    {
        $client = static::createFreeWorkClient();

        $client->request('POST', '/mailjet/unsubscribe', [
            'json' => $payload,
        ]);

        if (null === $container = $client->getContainer()) {
            throw new \RuntimeException('Container is null');
        }
        $em = $container->get('doctrine')->getManager();

        $mailjetUnsubscribeLog = $em->getRepository(MailjetUnsubscribeLog::class)->findOneBy([], ['id' => 'desc']);

        self::assertNotNull($mailjetUnsubscribeLog);
        /* @var MailjetUnsubscribeLog  $mailjetUnsubscribeLog */
        self::assertFalse($mailjetUnsubscribeLog->getUnsubscribed());
        self::assertResponseIsSuccessful();
    }

    public function testWithValidData(): void
    {
        $client = static::createFreeWorkClient();
        if (null === $container = $client->getContainer()) {
            throw new \RuntimeException('Container is null');
        }
        $em = $container->get('doctrine')->getManager();

        /** @var User $user */
        $user = $em->getRepository(User::class)->findOneBy(['email' => 'claude.monet@free-work.fr']);

        self::assertTrue($user->getNotification()->getMarketingNewsletter());

        $client->request('POST', '/mailjet/unsubscribe', [
            'json' => [
                [
                    'event' => 'unsub',
                    'email' => 'claude.monet@free-work.fr',
                ],
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertFalse($user->getNotification()->getMarketingNewsletter());
    }
}
