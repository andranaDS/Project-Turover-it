<?php

namespace App\JobPosting\Tests\Functional\FreeWork\JobPosting;

use App\JobPosting\Entity\JobPosting;
use App\JobPosting\Entity\JobPostingUserFavorite;
use App\Tests\Functional\ApiTestCase;
use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class JobPostingFavoritePatchTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('PATCH', '/job_postings/1/favorite');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('PATCH', '/job_postings/1/favorite');

        self::assertResponseIsSuccessful();
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('PATCH', '/job_postings/1/favorite');

        self::assertResponseIsSuccessful();
    }

    public function testDeleteFavorite(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');

        $container = $client->getContainer();
        if (null === $container) {
            throw new \RuntimeException('Container is null');
        }
        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine')->getManager();
        $jobPosting = $em->find(JobPosting::class, 1);
        $user = $em->getRepository(User::class)->findOneBy([
            'email' => 'claude.monet@free-work.fr',
        ]);
        self::assertNotNull($jobPosting);
        self::assertNotNull($user);

        // 1 - before
        $jobPostingFavorite = $em->getRepository(JobPostingUserFavorite::class)->findOneBy([
            'user' => $user,
            'jobPosting' => $jobPosting,
        ]);
        self::assertNotNull($jobPostingFavorite);

        // 2 - remove to favorites
        $client->request('PATCH', '/job_postings/1/favorite');
        self::assertResponseStatusCodeSame(204);

        // 3 - after
        $jobPostingFavorite = $em->getRepository(JobPostingUserFavorite::class)->findOneBy([
            'user' => $user,
            'jobPosting' => $jobPosting,
        ]);
        self::assertNull($jobPostingFavorite);
    }

    public function testAddFavorite(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');

        $container = $client->getContainer();
        if (null === $container) {
            throw new \RuntimeException('Container is null');
        }
        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine')->getManager();
        $jobPosting = $em->find(JobPosting::class, 4);
        $user = $em->getRepository(User::class)->findOneBy([
            'email' => 'claude.monet@free-work.fr',
        ]);
        self::assertNotNull($jobPosting);
        self::assertNotNull($user);

        // 1 - before
        $jobPostingFavorite = $em->getRepository(JobPostingUserFavorite::class)->findOneBy([
            'user' => $user,
            'jobPosting' => $jobPosting,
        ]);
        self::assertNull($jobPostingFavorite);

        // 2 - add to favorites
        $client->request('PATCH', '/job_postings/4/favorite');
        self::assertResponseStatusCodeSame(201);

        // 3 - after
        $jobPostingFavorite = $em->getRepository(JobPostingUserFavorite::class)->findOneBy([
            'user' => $user,
            'jobPosting' => $jobPosting,
        ]);
        self::assertNotNull($jobPostingFavorite);
    }
}
