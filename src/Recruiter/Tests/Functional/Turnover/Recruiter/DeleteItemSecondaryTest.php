<?php

namespace App\Recruiter\Tests\Functional\Turnover\Recruiter;

use App\Recruiter\Entity\Recruiter;
use App\Tests\Functional\ApiTestCase;

class DeleteItemSecondaryTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createTurnoverClient();
        $client->request('DELETE', '/companies/mine/recruiters/2');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsNotOwner(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('DELETE', '/companies/mine/recruiters/4');

        self::assertResponseStatusCodeSame(403);
    }

    public function testNotFoundUser(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('DELETE', '/companies/mine/recruiters/not-found');

        self::assertResponseStatusCodeSame(404);
    }

    public function testLoggedAsOwner(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $em = $this->getEntityManager($client);

        $recruiter = $em->getRepository(Recruiter::class)->findOneById(2);
        $company = $recruiter->getCompany();

        // 1 - before
        // 1.1 - recruiter
        self::assertNotNull($recruiter);
        self::assertNotNull($recruiter->getEmail());
        self::assertNotNull($recruiter->getUsername());
        self::assertNotNull($recruiter->getFirstName());
        self::assertNotNull($recruiter->getLastName());
        self::assertNotNull($recruiter->getGender());
        self::assertNotNull($recruiter->getPhoneNumber());
        self::assertTrue($recruiter->isEnabled());
        self::assertNotNull($recruiter->getCompany());
        self::assertNotNull($recruiter->getJob());
        self::assertFalse($recruiter->isMain());
        self::assertFalse($recruiter->isTermsOfService());
        self::assertNull($recruiter->getTermsOfServiceAcceptedAt());
        self::assertNull($recruiter->getDeletedAt());

        // 1.2 - company
        self::assertSame('Company 1', $company->getName());
        self::assertSame(1, $recruiter->getCompany()->getUserFavorites()->count());
        self::assertNull($company->getDeletedAt());

        // 2 - delete
        $client->request('DELETE', '/companies/mine/recruiters/2');
        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(204);

        // 3 - after
        // 3.1 check recruiter
        self::assertNotNull($recruiter);
        self::assertNull($recruiter->getEmail());
        self::assertNull($recruiter->getUsername());
        self::assertNull($recruiter->getFirstName());
        self::assertNull($recruiter->getLastName());
        self::assertNull($recruiter->getGender());
        self::assertNull($recruiter->getPhoneNumber());
        self::assertFalse($recruiter->isEnabled());
        self::assertNotNull($recruiter->getCompany());
        self::assertNull($recruiter->getJob());
        self::assertFalse($recruiter->isMain());
        self::assertNotNull($recruiter->getCreatedBy());
        self::assertFalse($recruiter->isTermsOfService());
        self::assertNull($recruiter->getTermsOfServiceAcceptedAt());
        self::assertNull($recruiter->getWebinarViewedAt());
        self::assertNotNull($recruiter->getDeletedAt());

        // 3.2 check company (not deleted)
        self::assertSame('Company 1', $company->getName());
        self::assertSame(1, $recruiter->getCompany()->getUserFavorites()->count());
        self::assertNull($company->getDeletedAt());
    }
}
