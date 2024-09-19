<?php

namespace App\Core\Tests\Functional\Contact;

use App\Tests\Functional\ApiTestCase;
use Symfony\Component\String\ByteString;

class ContactPostTest extends ApiTestCase
{
    public function testWithValidData(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('POST', '/contacts', [
            'json' => [
                'fullname' => 'Berthe Morisot',
                'service' => 'users',
                'email' => 'berthe.morisot@free-work.fr',
                'subject' => 'Contact - Subject - 1 - new',
                'message' => 'Contact - Message - 1 - new',
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/Contact',
            '@type' => 'Contact',
            'fullname' => 'Berthe Morisot',
            'email' => 'berthe.morisot@free-work.fr',
            'service' => 'users',
            'subject' => 'Contact - Subject - 1 - new',
            'message' => 'Contact - Message - 1 - new',
        ]);

        $email = self::getMailerMessage();
        self::assertNotNull($email);
        self::assertEmailHeaderSame($email, 'from', 'Free-Work <contact@free-work.com>');
        self::assertEmailHeaderSame($email, 'to', 'users@free-work.fr');
        self::assertEmailHeaderSame($email, 'subject', 'TEST: Demande de contact');
        self::assertEmailTextBodyContains($email, 'Berthe Morisot');
        self::assertEmailTextBodyContains($email, 'berthe.morisot@free-work.fr');
        self::assertEmailTextBodyContains($email, 'berthe.morisot@free-work.fr');
        self::assertEmailTextBodyContains($email, 'Contact - Subject - 1 - new');
        self::assertEmailTextBodyContains($email, 'Contact - Message - 1 - new');
    }

    public function testWithInvalidData(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('POST', '/contacts', [
            'json' => [
                'fullname' => ByteString::fromRandom(256),
                'service' => ByteString::fromRandom(10),
                'email' => ByteString::fromRandom(255),
                'subject' => ByteString::fromRandom(256),
                'message' => 'Forbidden content 1',
            ],
        ]);
        self::assertResponseStatusCodeSame(422);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'violations' => [
                [
                    'propertyPath' => 'fullname',
                    'message' => 'Cette chaîne est trop longue. Elle doit avoir au maximum 255 caractères.',
                ],
                [
                    'propertyPath' => 'email',
                    'message' => 'Cette valeur n\'est pas une adresse email valide.',
                ],
                [
                    'propertyPath' => 'service',
                    'message' => 'Cette valeur doit être l\'un des choix proposés.',
                ],
                [
                    'propertyPath' => 'subject',
                    'message' => 'Cette chaîne est trop longue. Elle doit avoir au maximum 255 caractères.',
                ],
                [
                    'propertyPath' => 'message',
                    'message' => 'La valeur est constitué d\'élement(s) interdit: "forbidden content 1".',
                ],
            ],
        ]);
    }

    public function testWithMissingData(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('POST', '/contacts', [
            'json' => [
                'fullname' => '',
                'service' => '',
                'email' => '',
                'subject' => '',
                'message' => '',
            ],
        ]);
        self::assertResponseStatusCodeSame(422);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'violations' => [
                [
                    'propertyPath' => 'fullname',
                    'message' => 'Cette valeur ne doit pas être vide.',
                ],
                [
                    'propertyPath' => 'email',
                    'message' => 'Cette valeur ne doit pas être vide.',
                ],
                [
                    'propertyPath' => 'service',
                    'message' => 'Cette valeur ne doit pas être vide.',
                ],
                [
                    'propertyPath' => 'service',
                    'message' => 'Cette valeur doit être l\'un des choix proposés.',
                ],
                [
                    'propertyPath' => 'subject',
                    'message' => 'Cette valeur ne doit pas être vide.',
                ],
                [
                    'propertyPath' => 'message',
                    'message' => 'Cette valeur ne doit pas être vide.',
                ],
            ],
        ]);
    }
}
