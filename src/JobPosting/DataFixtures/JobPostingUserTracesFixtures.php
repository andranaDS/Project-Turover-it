<?php

namespace App\JobPosting\DataFixtures;

use App\Core\DataFixtures\AbstractFixture;
use App\Core\Util\Arrays;
use App\JobPosting\Entity\JobPosting;
use App\JobPosting\Entity\JobPostingUserTrace;
use App\User\DataFixtures\UsersFixtures;
use App\User\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class JobPostingUserTracesFixtures extends AbstractFixture implements DependentFixtureInterface
{
    private array $jobPostings = [];
    private array $users = [];

    public function load(ObjectManager $manager): void
    {
        // fetch job_postings
        foreach ($manager->getRepository(JobPosting::class)->findAll() as $jobPosting) {
            /* @var JobPosting $jobPosting */
            $this->jobPostings[$jobPosting->getId()] = $jobPosting;
        }

        // fetch users
        foreach ($manager->getRepository(User::class)->findAll() as $user) {
            /* @var User $user */
            $this->users[$user->getEmail()] = $user;
        }

        // process data
        foreach ($this->getData() as $d) {
            $trace = (new JobPostingUserTrace())
                ->setUser($d['user'] ?? null)
                ->setJobPosting($d['jobPosting'])
                ->setReadAt($d['readAt'])
                ->setIp($d['ip'])
            ;

            $manager->persist($trace);
        }

        $manager->flush();
    }

    public function getDevData(): array
    {
        $faker = Factory::create('fr_Fr');

        $data = [];

        $tracesCount = 10000;
        for ($i = 0; $i < $tracesCount; ++$i) {
            $data[] = [
                'jobPosting' => Arrays::getRandom($this->jobPostings),
                'user' => 0 === random_int(0, 1) ? null : Arrays::getRandom($this->users),
                'readAt' => $faker->dateTimeBetween('-1 year'),
                'ip' => $faker->ipv4(),
            ];
        }

        usort($data, function (array $a, array $b) {
            return $a['readAt'] <=> $b['readAt'];
        });

        return $data;
    }

    public function getTestData(): array
    {
        return [
            [
                'jobPosting' => $this->jobPostings[1],
                'user' => $this->users['claude.monet@free-work.fr'],
                'readAt' => new \DateTime('2021-01-01 13:30:00'),
                'ip' => '1.2.3.4',
            ],
            [
                'jobPosting' => $this->jobPostings[1],
                'user' => $this->users['claude.monet@free-work.fr'],
                'readAt' => new \DateTime('2021-01-01 13:35:00'),
                'ip' => '1.2.3.5',
            ],
            [
                'jobPosting' => $this->jobPostings[2],
                'user' => null,
                'readAt' => new \DateTime('2021-01-01 13:40:00'),
                'ip' => '1.2.3.6',
            ],
            [
                'jobPosting' => $this->jobPostings[3],
                'user' => $this->users['auguste.renoir@free-work.fr'],
                'readAt' => new \DateTime('2021-01-01 13:45:00'),
                'ip' => '1.2.3.7',
            ],
        ];
    }

    public function getDependencies()
    {
        return [
            JobPostingsFixtures::class,
            UsersFixtures::class,
        ];
    }
}
