<?php

namespace App\JobPosting\Tests\Functional\Turnover\JobPosting;

use App\JobPosting\Entity\JobPosting;
use App\Tests\Functional\ApiTestCase;
use Doctrine\ORM\EntityManagerInterface;

class JobPostingDeleteTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createTurnoverClient();
        $client->request('DELETE', '/job_postings/1');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsNotOwner(): void
    {
        $client = static::createTurnoverAuthenticatedClient('jesse.pinkman@breaking-bad.com');
        $client->request('DELETE', '/job_postings/1');

        self::assertResponseStatusCodeSame(403);
    }

    public function testLoggedAsOwner(): void
    {
        $client = static::createTurnoverAuthenticatedClient();

        $container = $client->getContainer();
        if (null === $container) {
            throw new \RuntimeException('Container is null');
        }

        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine')->getManager();
        $jobPosting = $em->getRepository(JobPosting::class)->findOneBy(['id' => 1]);
        self::assertNull($jobPosting->getDeletedAt());

        $client->request('DELETE', '/job_postings/1');

        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(204);
        self::assertNotNull($jobPosting->getDeletedAt());
    }
}
