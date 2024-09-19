<?php

namespace App\User\DataFixtures;

use App\Core\DataFixtures\AbstractFixture;
use App\Core\DataFixtures\JobFixtures;
use App\Core\Entity\Job;
use App\Core\Util\Arrays;
use App\User\Entity\User;
use App\User\Entity\UserJob;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class UserJobsFixtures extends AbstractFixture implements DependentFixtureInterface
{
    private array $users = [];
    private array $jobs = [];

    public function load(ObjectManager $manager): void
    {
        // fetch users
        foreach ($manager->getRepository(User::class)->findAll() as $user) {
            /* @var User $user */
            if ($user->getProfileJobTitle()) {
                $this->users[$user->getEmail()] = $user;
            }
        }

        // fetch jobs
        foreach ($manager->getRepository(Job::class)->findAll() as $job) {
            /* @var Job $job */
            $this->jobs[$job->getId()] = $job;
        }

        foreach ($this->getData() as $d) {
            $userJob = (new UserJob())
                ->setUser($d['user'])
                ->setJob($d['job'])
                ->setMain($d['main'])
            ;
            $manager->persist($userJob);
        }

        $manager->flush();
    }

    public function getDevData(): array
    {
        $data = [];

        foreach ($this->users as $user) {
            $jobsCount = random_int(6, 16);
            $jobsMainCount = random_int(0, 3);
            for ($i = 0; $i < $jobsCount; ++$i) {
                $data[] = [
                    'user' => $user,
                    'job' => Arrays::getRandom($this->jobs),
                    'main' => $i < $jobsMainCount,
                ];
            }
        }

        return $data;
    }

    public function getTestData(): array
    {
        return [
            [
                'user' => $this->users['claude.monet@free-work.fr'],
                'job' => $this->jobs[19],
                'main' => true,
            ],
            [
                'user' => $this->users['claude.monet@free-work.fr'],
                'job' => $this->jobs[75],
                'main' => true,
            ],
            [
                'user' => $this->users['vincent.van-gogh@free-work.fr'],
                'job' => $this->jobs[7],
                'main' => true,
            ],
            [
                'user' => $this->users['vincent.van-gogh@free-work.fr'],
                'job' => $this->jobs[17],
                'main' => true,
            ],
            [
                'user' => $this->users['auguste.renoir@free-work.fr'],
                'job' => $this->jobs[177],
                'main' => true,
            ],
        ];
    }

    public static function getGroups(): array
    {
        return ['user'];
    }

    public function getDependencies(): array
    {
        return [
            JobFixtures::class,
            UsersFixtures::class,
        ];
    }
}
