<?php

namespace App\Recruiter\Tests\Functional\Turnover\Recruiter;

use App\Recruiter\Entity\Recruiter;
use App\Tests\Functional\ApiTestCase;

class PatchWebinarTest extends ApiTestCase
{
    public static function provideValidCases(): iterable
    {
        return [
            ['/recruiters/me/webinar'],
            ['/recruiters/1/webinar'],
        ];
    }

    /**
     * @dataProvider provideValidCases
     */
    public function testValidCases(string $path): void
    {
        $email = 'walter.white@breaking-bad.com';
        $client = static::createTurnoverAuthenticatedClient($email);

        $repository = $client->getContainer()->get('doctrine')->getManager()->getRepository(Recruiter::class);
        $recruiter = $repository->findOneByEmail($email);

        self::assertInstanceOf(Recruiter::class, $recruiter);
        self::assertNull($recruiter->getWebinarViewedAt());

        $client->request('PATCH', $path);
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
                'slug' => 'company-1',
            ],
            'main' => true,
            'job' => 'CTO',
            'termsOfService' => true,
            'gender' => 'male',
        ]);

        self::assertEqualsWithDelta(time(), $recruiter->getWebinarViewedAt()->getTimestamp(), 2);
    }

    public function testNotLoggedCases(): void
    {
        $client = static::createTurnoverClient();
        $client->request('PATCH', '/recruiters/2/webinar');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedOnOtherRecruiter(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('PATCH', '/recruiters/2/webinar');

        self::assertResponseStatusCodeSame(403);
    }
}
