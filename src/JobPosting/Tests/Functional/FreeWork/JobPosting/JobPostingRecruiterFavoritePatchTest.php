<?php

namespace App\JobPosting\Tests\Functional\FreeWork\JobPosting;

use App\JobPosting\Entity\JobPosting;
use App\JobPosting\Entity\JobPostingRecruiterFavorite;
use App\Recruiter\Entity\Recruiter;
use App\Tests\Functional\ApiTestCase;
use Doctrine\ORM\EntityManagerInterface;

class JobPostingRecruiterFavoritePatchTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createTurnoverClient();
        $client->request('PATCH', '/job_postings/1/recruiter/favorite');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('PATCH', '/job_postings/1/recruiter/favorite');

        self::assertResponseIsSuccessful();
    }

    public function testDeleteFavorite(): void
    {
        $client = static::createTurnoverAuthenticatedClient();

        $container = $client->getContainer();
        if (null === $container) {
            throw new \RuntimeException('Container is null');
        }
        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine')->getManager();
        $jobPosting = $em->find(JobPosting::class, 1);
        $recruiter = $em->getRepository(Recruiter::class)->findOneBy([
            'email' => 'walter.white@breaking-bad.com',
        ]);
        self::assertNotNull($jobPosting);
        self::assertNotNull($recruiter);

        // 1 - before
        $jobPostingRecruiterFavorite = $em->getRepository(JobPostingRecruiterFavorite::class)->findOneBy([
            'recruiter' => $recruiter,
            'jobPosting' => $jobPosting,
        ]);
        self::assertNotNull($jobPostingRecruiterFavorite);

        // 2 - remove to favorites
        $client->request('PATCH', '/job_postings/1/recruiter/favorite');
        self::assertResponseStatusCodeSame(204);

        // 3 - after
        $jobPostingRecruiterFavorite = $em->getRepository(JobPostingRecruiterFavorite::class)->findOneBy([
            'recruiter' => $recruiter,
            'jobPosting' => $jobPosting,
        ]);
        self::assertNull($jobPostingRecruiterFavorite);
    }

    public function testAddFavorite(): void
    {
        $client = static::createTurnoverAuthenticatedClient();

        $container = $client->getContainer();
        if (null === $container) {
            throw new \RuntimeException('Container is null');
        }
        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine')->getManager();
        $jobPosting = $em->find(JobPosting::class, 4);
        $recruiter = $em->getRepository(Recruiter::class)->findOneBy([
            'email' => 'walter.white@breaking-bad.com',
        ]);
        self::assertNotNull($jobPosting);
        self::assertNotNull($recruiter);

        // 1 - before
        $jobPostingRecruiterFavorite = $em->getRepository(JobPostingRecruiterFavorite::class)->findOneBy([
            'recruiter' => $recruiter,
            'jobPosting' => $jobPosting,
        ]);
        self::assertNull($jobPostingRecruiterFavorite);

        // 2 - add to favorites
        $client->request('PATCH', '/job_postings/4/recruiter/favorite');
        self::assertResponseStatusCodeSame(201);

        // 3 - after
        $jobPostingRecruiterFavorite = $em->getRepository(JobPostingRecruiterFavorite::class)->findOneBy([
            'recruiter' => $recruiter,
            'jobPosting' => $jobPosting,
        ]);
        self::assertNotNull($jobPostingRecruiterFavorite);
    }
}
