<?php

namespace App\User\Tests\Functional\User;

use App\Tests\Functional\ApiTestCase;
use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserDeleteTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('DELETE', '/users/1');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('DELETE', '/users/1');

        self::assertResponseStatusCodeSame(403);
    }

    public function testLoggedAsOwner(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('DELETE', '/users/6');

        self::assertResponseIsSuccessful();
    }

    public function testLoggedAsNotOwner(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('vincent.van-gogh@free-work.fr');
        $client->request('DELETE', '/users/6');

        self::assertResponseStatusCodeSame(403);
    }

    public function testNonExistantUser(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('DELETE', '/users/non-existant');

        self::assertResponseStatusCodeSame(404);
    }

    public function testDelete(): void
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
            'id' => 6,
        ]);
        self::assertNotNull($user);
        self::assertNotNull($user->getEmail());
        self::assertNotNull($user->getNickname());
        self::assertNotNull($user->getFirstName());
        self::assertNotNull($user->getLastName());
        self::assertNotNull($user->getGender());
        self::assertNotNull($user->getJobTitle());
        self::assertNotNull($user->getWebsite());
        self::assertNotNull($user->getProfileJobTitle());
        self::assertNotNull($user->getExperienceYear());
        self::assertNotNull($user->getAvailability());
        self::assertNotNull($user->getNextAvailabilityAt());
        self::assertNotNull($user->getNotification());
        self::assertTrue($user->getVisible());
        self::assertNotNull($user->getFormStep());
        self::assertTrue($user->getProfileCompleted());
        self::assertNotNull($user->getProfileWebsite());
        self::assertNotNull($user->getProfileLinkedInProfile());
        self::assertNotNull($user->getProfileProjectWebsite());
        self::assertNotNull($user->getFreelanceLegalStatus());
        self::assertNotNull($user->getEmploymentTime());
        self::assertNotNull($user->getFreelanceCurrency());
        self::assertNotNull($user->getEmployeeCurrency());
        self::assertNotNull($user->getCompanyCountryCode());
        self::assertNotNull($user->getIntroduceYourself());
        self::assertNotNull($user->getSignature());
        self::assertNotNull($user->getAvatar());
        self::assertNotNull($user->getGrossAnnualSalary());
        self::assertNotNull($user->getAverageDailyRate());
        self::assertNotNull($user->getCompanyRegistrationNumber());
        self::assertNotNull($user->getBirthdate());
        if (null !== $location = $user->getLocation()) {
            self::assertNotNull($location->getLabel());
            self::assertNotNull($location->getLatitude());
            self::assertNotNull($location->getLongitude());
            self::assertNotNull($location->getLocality());
            self::assertNotNull($location->getAdminLevel1());
            self::assertNotNull($location->getCountry());
            self::assertNotNull($location->getCountryCode());
        }
        self::assertNotNull($user->getFormation());
        self::assertNotEmpty($user->getBlogComments()->getValues());
        self::assertNotEmpty($user->getDocuments()->getValues());
        self::assertNotEmpty($user->getLocations()->getValues());
        self::assertNotEmpty($user->getJobPostingSearches()->getValues());
        self::assertNotEmpty($user->getSkills()->getValues());
        self::assertNotEmpty($user->getLanguages()->getValues());
        self::assertNotEmpty($user->getJobs()->getValues());
        self::assertNotEmpty($user->getSoftSkills()->getValues());
        self::assertNotEmpty($user->getApplications()->getValues());
        self::assertNotNull($user->getProviders()->getValues());

        self::assertNull($user->getDeletedAt());

        self::assertCount(9, $user->getForumPosts());

        // user login
        $client->request('GET', '/users/6/data');
        self::assertResponseIsSuccessful();

        // 2 - delete user
        $client->request('DELETE', '/users/6');
        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(204);

        $filter = $em->getFilters()->enable('soft_deleteable'); /* @phpstan-ignore-line */
        $filter->disableForEntity(User::class);

        // 3 - after
        $user = $em->getRepository(User::class)->findOneBy([
            'id' => 6,
        ]);

        self::assertNotNull($user);
        self::assertNull($user->getEmail());
        self::assertNull($user->getPlainPassword());
        self::assertNull($user->getNickname());
        self::assertNull($user->getFirstName());
        self::assertNull($user->getLastName());
        self::assertNull($user->getGender());
        self::assertNull($user->getJobTitle());
        self::assertNull($user->getWebsite());
        self::assertNull($user->getProfileJobTitle());
        self::assertNull($user->getExperienceYear());
        self::assertNull($user->getAvailability());
        self::assertNull($user->getNextAvailabilityAt());
        self::assertNull($user->getVisible());
        self::assertNull($user->getFormStep());
        self::assertNull($user->getNotification());
        self::assertFalse($user->getProfileCompleted());
        self::assertNull($user->getProfileWebsite());
        self::assertNull($user->getProfileLinkedInProfile());
        self::assertNull($user->getProfileProjectWebsite());
        self::assertNull($user->getFreelanceLegalStatus());
        self::assertNull($user->getEmploymentTime());
        self::assertNull($user->getFreelanceCurrency());
        self::assertNull($user->getEmployeeCurrency());
        self::assertNull($user->getCompanyCountryCode());
        self::assertNull($user->getIntroduceYourself());
        self::assertNull($user->getSignature());
        self::assertNull($user->getAvatar());
        self::assertNull($user->getGrossAnnualSalary());
        self::assertNull($user->getAverageDailyRate());
        self::assertNull($user->getCompanyRegistrationNumber());
        self::assertNull($user->getBirthdate());
        if (null !== $location = $user->getLocation()) {
            self::assertNull($location->getLabel());
            self::assertNull($location->getLatitude());
            self::assertNull($location->getLongitude());
            self::assertNull($location->getLocality());
            self::assertNull($location->getAdminLevel1());
            self::assertNull($location->getCountry());
            self::assertNull($location->getCountryCode());
        }
        self::assertNull($user->getFormation());
        // self::assertEmpty($user->getBlogComments()->getValues()); TODO ticket FW
        self::assertEmpty($user->getDocuments()->getValues());
        self::assertEmpty($user->getLocations()->getValues());
        self::assertEmpty($user->getJobPostingSearches()->getValues());
        self::assertEmpty($user->getSkills()->getValues());
        self::assertEmpty($user->getLanguages()->getValues());
        self::assertEmpty($user->getJobs()->getValues());
        self::assertEmpty($user->getSoftSkills()->getValues());
        // self::assertEmpty($user->getApplications()->getValues()); TODO ticket FW
        self::assertEmpty($user->getProviders()->getValues());

        self::assertNotNull($user->getDeletedAt());

        self::assertCount(9, $user->getForumPosts());

        // user logout
        $client->request('GET', '/users/6/data');
        self::assertResponseStatusCodeSame(401);
    }
}
