<?php

namespace App\Messaging\Tests\Functional\Message;

use App\Messaging\Entity\Message;
use App\Tests\Functional\ApiTestCase;
use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\ByteString;

class MessagePostMessageTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();

        $client->request('POST', '/feeds/1/messages');
        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedOnUnauthorizedFeed(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('henri.matisse@free-work.fr');
        $client->request('POST', '/feeds/1/messages');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsAuthorizeUser(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('POST', '/feeds/1/messages');

        self::assertResponseStatusCodeSame(422);
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient(); // id 2
        $client->request('POST', '/feeds/1/messages');

        self::assertResponseStatusCodeSame(401);
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
        ];
    }

    /**
     * @dataProvider provideInvalidDataAsAuthorizeUserCases
     */
    public function testInvalidDataAsAuthorizeUser(array $payload, array $expected): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('POST', '/feeds/1/messages', $payload);

        self::assertResponseStatusCodeSame(422);
        self::assertJsonContains($expected);
    }

    public static function provideInvalidFileDataAsAuthorizeUserCases(): iterable
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
            'wrong mime type' => [
                __DIR__ . '/../../Data/message-document-mime-type.txt',
                'message-document-wrong-mime-type.txt',
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'documentFile',
                            'message' => 'Le type du fichier est invalide. Les types autorisés sont .pdf, .jpeg, .png, .jpg.',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideInvalidFileDataAsAuthorizeUserCases
     */
    public function testInvalidFileDataAsAuthorizeUser(string $documentPath, string $documentName, array $expected): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');

        $file = new UploadedFile(
            $documentPath,
            $documentName,
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        );

        $client->request('POST', '/feeds/1/messages', [
            'headers' => ['Content-Type' => 'multipart/form-data'],
            'extra' => [
                'parameters' => [
                    'contentHtml' => '<p>Feed 1 - Message</p>',
                    'contentJson' => '{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text","text":"Feed 1 - Message"}]}]}',
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

    public function testValidDataAsAuthorizeUser(): void
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
        $message = $em->getRepository(Message::class)->findOneById(10);
        self::assertNull($message);

        // 2 - post file
        $originalName = 'message-1.jpg';
        $file = new UploadedFile(
            'src/Messaging/DataFixtures/files/message-1.jpg',
            $originalName,
            'image/jpeg',
        );

        $client->request('POST', '/feeds/1/messages', [
            'headers' => ['Content-Type' => 'multipart/form-data'],
            'extra' => [
                'parameters' => [
                    'contentHtml' => '<p>Feed 1 - Message</p>',
                    'contentJson' => '{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text","text":"Feed 1 - Message"}]}]}',
                ],
                'files' => [
                    'documentFile' => $file,
                ],
            ],
        ]);

        self::assertEmailCount(1);

        $rawMessage = self::getMailerMessage();

        self::assertNotNull($rawMessage);
        self::assertEmailHeaderSame($rawMessage, 'from', 'Free-Work <forum@free-work.com>');
        self::assertEmailHeaderSame($rawMessage, 'to', 'vincent.van-gogh@free-work.fr');
        self::assertEmailHeaderSame($rawMessage, 'subject', 'TEST: Nouveau message d’un Free-worker');
        self::assertEmailTextBodyContains($rawMessage, 'https://front.freework.localhost/inbox?t=1');

        // 3 - after
        $user = $em->getRepository(User::class)->findOneBy([
            'email' => 'claude.monet@free-work.fr',
        ]);
        self::assertNotNull($user);
        $message = $em->getRepository(Message::class)->findOneById(10);
        self::assertNotNull($message);

        self::assertResponseStatusCodeSame(201);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/Message',
            '@type' => 'Message',
            'id' => 10,
            'content' => 'Feed 1 - Message',
            'contentHtml' => '<p>Feed 1 - Message</p>',
            'contentJson' => '{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text","text":"Feed 1 - Message"}]}]}',
            'document' => 'https://s1-storage.dev.free-work.com/message/documents/' . $message->getDocument(),
            'documentOriginalName' => $originalName,
            'author' => '/users/6',
            'feed' => '/feeds/1',
        ]);
    }
}
