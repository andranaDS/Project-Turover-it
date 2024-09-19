<?php

namespace App\Messaging\Tests\Functional\Feed;

use App\Messaging\Entity\Message;
use App\Tests\Functional\ApiTestCase;
use App\User\Entity\User;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\ByteString;

class FeedPostTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();

        $client->request('POST', '/feeds');
        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('POST', '/feeds');

        self::assertResponseStatusCodeSame(422);
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient(); // id 2
        $client->request('POST', '/feeds');

        self::assertResponseStatusCodeSame(422);
    }

    public static function provideInvalidDataAsAuthorizeUserCases(): iterable
    {
        return [
            'blank' => [
                [
                    'headers' => ['Content-Type' => 'multipart/form-data'],
                    'extra' => [
                        'parameters' => [
                            'contentHtml' => '',
                            'contentJson' => '',
                            'user' => 2,
                        ],
                        'files' => [
                        ],
                    ],
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'contentHtml',
                            'message' => 'Cette valeur ne doit pas être vide.',
                        ],
                        [
                            'propertyPath' => 'contentJson',
                            'message' => 'Cette valeur ne doit pas être vide.',
                        ],
                    ],
                ],
            ],
            'invalid' => [
                [
                    'headers' => ['Content-Type' => 'multipart/form-data'],
                    'extra' => [
                        'parameters' => [
                            'contentHtml' => ByteString::fromRandom(256),
                            'contentJson' => ByteString::fromRandom(256),
                            'user' => 2,
                        ],
                        'files' => [
                        ],
                    ],
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'contentJson',
                            'message' => 'Cette valeur doit être un JSON valide.',
                        ],
                    ],
                ],
            ],
            'invalid_user' => [
                [
                    'headers' => ['Content-Type' => 'multipart/form-data'],
                    'extra' => [
                        'parameters' => [
                            'contentHtml' => ByteString::fromRandom(255),
                            'contentJson' => ByteString::fromRandom(255),
                            'user' => '',
                        ],
                        'files' => [
                        ],
                    ],
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'user',
                            'message' => 'Cette valeur ne doit pas être vide.',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideInvalidDataAsAuthorizeUserCases
     */
    public function testInvalidDataAsAuthorizeUser(array $payload, array $expected): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('POST', '/feeds', $payload);

        self::assertResponseStatusCodeSame(422);
        self::assertJsonContains($expected);
    }

    public static function provideInvalidFileDataAsUserCases(): iterable
    {
        return [
            'too large' => [
                __DIR__ . '/../../Data/message-document-too-large.docx',
                'message-document-too-large.docx',
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
        ];
    }

    /**
     * @dataProvider provideInvalidFileDataAsUserCases
     */
    public function testInvalidFileDataAsUser(string $documentPath, string $documentName, array $expected): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');

        $file = new UploadedFile(
            $documentPath,
            $documentName,
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        );

        $client->request('POST', '/feeds', [
            'headers' => ['Content-Type' => 'multipart/form-data'],
            'extra' => [
                'parameters' => [
                    'contentHtml' => '<p>Feed 1 - Message</p>',
                    'contentJson' => '{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text","text":"Feed 1 - Message"}]}]}',
                    'user' => 2,
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

    public function testValidDataAsUserOnNewFeed(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');

        $container = $client->getContainer();
        if (null === $container) {
            throw new \RuntimeException('Container is null');
        }
        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine')->getManager();

        // 1 - before
        $user = $em->getRepository(User::class)->findOneBy([
            'email' => 'claude.monet@free-work.fr',
        ]);
        self::assertNotNull($user);

        // 2 - post file
        $file = new UploadedFile(
            'src/Messaging/DataFixtures/files/message-1.jpg',
            'message-1.jpg',
            'image/jpeg',
        );

        $client->request('POST', '/feeds', [
            'headers' => ['Content-Type' => 'multipart/form-data'],
            'extra' => [
                'parameters' => [
                    'contentHtml' => '<p>Feed 4 new - Message</p>',
                    'contentJson' => '{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text","text":"Feed 4 new - Message"}]}]}',
                    'user' => 10,
                ],
                'files' => [
                    'documentFile' => $file,
                ],
            ],
        ]);

        // 3 - after
        $user = $em->getRepository(User::class)->findOneBy([
            'email' => 'claude.monet@free-work.fr',
        ]);
        self::assertNotNull($user);
        $message = $em->getRepository(Message::class)->findOneBy(['author' => $user], ['id' => Criteria::DESC]); // TODO we can't use id because auto-increment
        self::assertNotNull($message);

        self::assertResponseStatusCodeSame(201);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/Feed',
            '@type' => 'Feed',
            'id' => 4,
            'messages' => [
                [
                    'id' => 8,
                    'content' => 'Feed 4 new - Message',
                    'contentHtml' => '<p>Feed 4 new - Message</p>',
                    'contentJson' => '{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text","text":"Feed 4 new - Message"}]}]}',
                    'document' => 'https://s1-storage.dev.free-work.com/message/documents/' . $message->getDocument(),
                ],
            ],
        ]);

        $rawMessage = self::getMailerMessage();

        self::assertNotNull($rawMessage);
        self::assertEmailHeaderSame($rawMessage, 'from', 'Free-Work <forum@free-work.com>');
        self::assertEmailHeaderSame($rawMessage, 'to', 'pablo.picasso@free-work.fr');
        self::assertEmailHeaderSame($rawMessage, 'subject', 'TEST: Nouveau message d’un Free-worker');
        self::assertEmailTextBodyContains($rawMessage, 'https://front.freework.localhost/inbox?t=4');
    }

    public function testValidDataAsUserOnExistingFeed(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');

        $container = $client->getContainer();
        if (null === $container) {
            throw new \RuntimeException('Container is null');
        }
        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine')->getManager();

        // 1 - before
        $user = $em->getRepository(User::class)->findOneBy([
            'email' => 'claude.monet@free-work.fr',
        ]);
        self::assertNotNull($user);

        // 2 - post file
        $file = new UploadedFile(
            'src/Messaging/DataFixtures/files/message-1.jpg',
            'message-1.jpg',
            'image/jpeg',
        );

        $client->request('POST', '/feeds', [
            'headers' => ['Content-Type' => 'multipart/form-data'],
            'extra' => [
                'parameters' => [
                    'contentHtml' => '<p>Feed 3 - Message new</p>',
                    'contentJson' => '{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text","text":"Feed 3 - Message new"}]}]}',
                    'user' => 9,
                ],
                'files' => [
                    'documentFile' => $file,
                ],
            ],
        ]);

        // 3 - after
        $user = $em->getRepository(User::class)->findOneBy([
            'email' => 'claude.monet@free-work.fr',
        ]);
        self::assertNotNull($user);
        $message = $em->getRepository(Message::class)->findOneBy(['author' => $user], ['id' => Criteria::DESC]);
        self::assertNotNull($message);

        self::assertResponseStatusCodeSame(201);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/Feed',
            '@type' => 'Feed',
            'id' => 3,
            'messages' => [
                [
                    'id' => 9,
                    'content' => 'Feed 3 - Message new',
                    'contentHtml' => '<p>Feed 3 - Message new</p>',
                    'contentJson' => '{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text","text":"Feed 3 - Message new"}]}]}',
                    'document' => 'https://s1-storage.dev.free-work.com/message/documents/' . $message->getDocument(),
                ],
            ],
        ]);

        self::assertEmailCount(1);

        $rawMessage = self::getMailerMessage();

        self::assertNotNull($rawMessage);
        self::assertEmailHeaderSame($rawMessage, 'from', 'Free-Work <forum@free-work.com>');
        self::assertEmailHeaderSame($rawMessage, 'to', 'henri.matisse@free-work.fr');
        self::assertEmailHeaderSame($rawMessage, 'subject', 'TEST: Nouveau message d’un Free-worker');
        self::assertEmailTextBodyContains($rawMessage, 'https://front.freework.localhost/inbox?t=3');
    }
}
