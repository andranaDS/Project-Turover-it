<?php

namespace App\Company\Tests\Functional\Turnover\Company;

use App\Company\Entity\Company;
use App\Company\Entity\CompanyRecruiterFavorite;
use App\Recruiter\Entity\Recruiter;
use App\Tests\Functional\ApiTestCase;
use Doctrine\ORM\EntityManagerInterface;

class CompanyFavoriteTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createTurnoverClient();
        $client->request('PATCH', '/companies/company-2/favorite');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('PATCH', '/companies/company-2/favorite');

        self::assertResponseIsSuccessful();
    }

    public function testDelete(): void
    {
        $client = static::createTurnoverAuthenticatedClient();

        if (null === $container = $client->getContainer()) {
            throw new \RuntimeException('Container is null');
        }

        $em = $container->get('doctrine')->getManager();
        $company = $em->find(Company::class, 3);
        $recruiter = $em->getRepository(Recruiter::class)->findOneBy(['email' => 'walter.white@breaking-bad.com']);
        self::assertNotNull($company);
        self::assertNotNull($recruiter);

        // 1 - before
        $companyFavorite = $em->getRepository(CompanyRecruiterFavorite::class)->findOneBy(['recruiter' => $recruiter, 'company' => $company]);
        self::assertNotNull($companyFavorite);

        // 2 - remove to favorites
        $client->request('PATCH', '/companies/company-3/favorite');
        self::assertResponseStatusCodeSame(204);

        // 3 - after
        $companyFavorite = $em->getRepository(CompanyRecruiterFavorite::class)->findOneBy(['recruiter' => $recruiter, 'company' => $company]);
        self::assertNull($companyFavorite);
    }

    public function testAdd(): void
    {
        $client = static::createTurnoverAuthenticatedClient();

        if (null === $container = $client->getContainer()) {
            throw new \RuntimeException('Container is null');
        }

        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine')->getManager();
        $company = $em->find(Company::class, 2);
        $recruiter = $em->getRepository(Recruiter::class)->findOneBy(['email' => 'walter.white@breaking-bad.com']);
        self::assertNotNull($company);
        self::assertNotNull($recruiter);

        // 1 - before
        $companyFavorite = $em->getRepository(CompanyRecruiterFavorite::class)->findOneBy(['recruiter' => $recruiter, 'company' => $company]);
        self::assertNull($companyFavorite);

        // 2 - remove to favorites
        $client->request('PATCH', '/companies/company-2/favorite');
        self::assertResponseStatusCodeSame(201);

        // 3 - after
        $companyFavorite = $em->getRepository(CompanyRecruiterFavorite::class)->findOneBy(['recruiter' => $recruiter, 'company' => $company]);
        self::assertNotNull($companyFavorite);
    }
}
