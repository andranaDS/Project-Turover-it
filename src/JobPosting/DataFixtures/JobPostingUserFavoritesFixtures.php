<?php

namespace App\JobPosting\DataFixtures;

use App\Core\DataFixtures\AbstractFixture;
use App\Core\Util\Arrays;
use App\JobPosting\Entity\JobPosting;
use App\JobPosting\Entity\JobPostingUserFavorite;
use App\User\DataFixtures\UsersFixtures;
use App\User\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class JobPostingUserFavoritesFixtures extends AbstractFixture implements DependentFixtureInterface
{
    private array $users;
    private array $jobs;

    public function load(ObjectManager $manager): void
    {
        // fetch all users
        foreach ($manager->getRepository(User::class)->findAll() as $user) {
            /* @var User $user */
            $this->users[$user->getEmail()] = $user;
        }

        // fetch all jobs
        foreach ($manager->getRepository(JobPosting::class)->findAll() as $jobPosting) {
            /* @var JobPosting $jobPosting */
            $this->jobs[$jobPosting->getTitle()] = $jobPosting;
        }

        // process data
        foreach ($this->getData() as $d) {
            $jobPostingFavorite = (new JobPostingUserFavorite())
                ->setJobPosting($d['jobPosting'])
                ->setUser($d['user'])
                ->setCreatedAt($d['createdAt'])
            ;

            $manager->persist($jobPostingFavorite);
        }

        $manager->flush();
    }

    public function getDevData(): array
    {
        $data = [];
        $faker = Factory::create('fr_Fr');

        foreach ($this->jobs as $job) {
            /** @var JobPosting $job */
            $jobFavoritesUsers = Arrays::getRandomSubarray($this->users, 0, 16);
            foreach ($jobFavoritesUsers as $jobFavoritesUser) {
                /* @var User $jobFavoritesUser */

                $data[] = [
                    'jobPosting' => $job,
                    'user' => $jobFavoritesUser,
                    'createdAt' => $faker->dateTimeBetween('- 6 months'),
                ];
            }
        }

        return $data;
    }

    public function getTestData(): array
    {
        return [
            [
                'jobPosting' => $this->jobs['Responsable applicatifs Finance (H/F) (CDI)'],
                'user' => $this->users['claude.monet@free-work.fr'],
                'createdAt' => new \DateTime('2021-03-01 23:30:00'),
            ],
            [
                'jobPosting' => $this->jobs['Responsable cybersécurité (sans management) (H/F)'],
                'user' => $this->users['claude.monet@free-work.fr'],
                'createdAt' => new \DateTime('2021-03-01 23:45:00'),
            ],
        ];
    }

    public function getDependencies()
    {
        return [
            UsersFixtures::class,
            JobPostingsFixtures::class,
        ];
    }
}
