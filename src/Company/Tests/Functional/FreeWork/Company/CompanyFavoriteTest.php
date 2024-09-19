<?php

namespace App\Company\Tests\Functional\FreeWork\Company;

use App\Company\Entity\Company;
use App\Company\Entity\CompanyUserFavorite;
use App\Tests\Functional\ApiTestCase;
use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class CompanyFavoriteTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('PATCH', '/companies/company-1/favorite');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('PATCH', '/companies/company-1/favorite');

        self::assertResponseIsSuccessful();
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('PATCH', '/companies/company-1/favorite');

        self::assertResponseIsSuccessful();
    }

    public function testDelete(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');

        if (null === $container = $client->getContainer()) {
            throw new \RuntimeException('Container is null');
        }

        $em = $container->get('doctrine')->getManager();
        $company = $em->find(Company::class, 1);
        $user = $em->getRepository(User::class)->findOneBy(['email' => 'claude.monet@free-work.fr']);
        self::assertNotNull($company);
        self::assertNotNull($user);

        // 1 - before
        $companyFavorite = $em->getRepository(CompanyUserFavorite::class)->findOneBy(['user' => $user, 'company' => $company]);
        self::assertNotNull($companyFavorite);

        // 2 - remove to favorites
        $client->request('PATCH', '/companies/company-1/favorite');
        self::assertResponseStatusCodeSame(204);

        // 3 - after
        $companyFavorite = $em->getRepository(CompanyUserFavorite::class)->findOneBy(['user' => $user, 'company' => $company]);
        self::assertNull($companyFavorite);
    }

    public function testAdd(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');

        if (null === $container = $client->getContainer()) {
            throw new \RuntimeException('Container is null');
        }

        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine')->getManager();
        $company = $em->find(Company::class, 2);
        $user = $em->getRepository(User::class)->findOneBy(['email' => 'claude.monet@free-work.fr']);
        self::assertNotNull($company);
        self::assertNotNull($user);

        // 1 - before
        $companyFavorite = $em->getRepository(CompanyUserFavorite::class)->findOneBy(['user' => $user, 'company' => $company]);
        self::assertNull($companyFavorite);

        // 2 - remove to favorites
        $client->request('PATCH', '/companies/company-2/favorite');
        self::assertResponseStatusCodeSame(201);

        // 3 - after
        $companyFavorite = $em->getRepository(CompanyUserFavorite::class)->findOneBy(['user' => $user, 'company' => $company]);
        self::assertNotNull($companyFavorite);
    }
}
