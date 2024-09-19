<?php

namespace App\Company\Tests\Functional\FreeWork\Company;

use App\Company\Entity\Company;
use App\Company\Entity\CompanyBlacklist;
use App\Tests\Functional\ApiTestCase;
use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class CompanyBlacklistTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('PATCH', '/companies/company-1/blacklist');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('PATCH', '/companies/company-1/blacklist');

        self::assertResponseIsSuccessful();
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('PATCH', '/companies/company-1/blacklist');

        self::assertResponseIsSuccessful();
    }

    public function testDelete(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('elisabeth.vigee-le-brun@free-work.fr');

        if (null === $container = $client->getContainer()) {
            throw new \RuntimeException('Container is null');
        }

        $em = $container->get('doctrine')->getManager();
        $company = $em->find(Company::class, 2);
        $user = $em->getRepository(User::class)->findOneBy(['email' => 'elisabeth.vigee-le-brun@free-work.fr']);
        self::assertNotNull($company);
        self::assertNotNull($user);

        // 1 - before
        $companyBlacklist = $em->getRepository(CompanyBlacklist::class)->findOneBy(['user' => $user, 'company' => $company]);
        self::assertNotNull($companyBlacklist);

        // 2 - remove to blacklists
        $client->request('PATCH', '/companies/company-2/blacklist');
        self::assertResponseStatusCodeSame(204);

        // 3 - after
        $companyBlacklist = $em->getRepository(CompanyBlacklist::class)->findOneBy(['user' => $user, 'company' => $company]);
        self::assertNull($companyBlacklist);
    }

    public function testAdd(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('elisabeth.vigee-le-brun@free-work.fr');

        if (null === $container = $client->getContainer()) {
            throw new \RuntimeException('Container is null');
        }

        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine')->getManager();
        $company = $em->find(Company::class, 3);
        $user = $em->getRepository(User::class)->findOneBy(['email' => 'elisabeth.vigee-le-brun@free-work.fr']);
        self::assertNotNull($company);
        self::assertNotNull($user);

        // 1 - before
        $companyBlacklist = $em->getRepository(CompanyBlacklist::class)->findOneBy(['user' => $user, 'company' => $company]);
        self::assertNull($companyBlacklist);

        // 2 - remove to blacklists
        $client->request('PATCH', '/companies/company-3/blacklist');
        self::assertResponseStatusCodeSame(201);

        // 3 - after
        $companyBlacklist = $em->getRepository(CompanyBlacklist::class)->findOneBy(['user' => $user, 'company' => $company]);
        self::assertNotNull($companyBlacklist);
    }
}
