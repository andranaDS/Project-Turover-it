<?php

namespace App\JobPosting\DataFixtures;

use App\Core\DataFixtures\AbstractFixture;
use App\Core\Util\Arrays;
use App\JobPosting\Entity\JobPosting;
use App\JobPosting\Entity\JobPostingRecruiterTrace;
use App\Recruiter\DataFixtures\RecruiterFixtures;
use App\Recruiter\Entity\Recruiter;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class JobPostingRecruiterTracesFixtures extends AbstractFixture implements DependentFixtureInterface
{
    private array $jobPostings = [];
    private array $recruiters = [];

    public function load(ObjectManager $manager): void
    {
        // fetch job_postings
        foreach ($manager->getRepository(JobPosting::class)->findAll() as $jobPosting) {
            /* @var JobPosting $jobPosting */
            $this->jobPostings[$jobPosting->getId()] = $jobPosting;
        }

        // fetch recruiters
        foreach ($manager->getRepository(Recruiter::class)->findAll() as $recruiter) {
            /* @var Recruiter $recruiter */
            $this->recruiters[$recruiter->getEmail()] = $recruiter;
        }

        // process data
        foreach ($this->getData() as $d) {
            $trace = (new JobPostingRecruiterTrace())
                ->setRecruiter($d['recruiter'] ?? null)
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
                'recruiter' => 0 === random_int(0, 1) ? null : Arrays::getRandom($this->recruiters),
                'readAt' => $faker->dateTimeBetween('-1 year'),
                'ip' => $faker->ipv4,
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
                'recruiter' => $this->recruiters['walter.white@breaking-bad.com'],
                'readAt' => new \DateTime('2021-01-01 13:30:00'),
                'ip' => '1.2.3.4',
            ],
            [
                'jobPosting' => $this->jobPostings[1],
                'recruiter' => $this->recruiters['walter.white@breaking-bad.com'],
                'readAt' => new \DateTime('2021-01-01 13:35:00'),
                'ip' => '1.2.3.5',
            ],
            [
                'jobPosting' => $this->jobPostings[2],
                'recruiter' => null,
                'readAt' => new \DateTime('2021-01-01 13:40:00'),
                'ip' => '1.2.3.6',
            ],
            [
                'jobPosting' => $this->jobPostings[3],
                'recruiter' => $this->recruiters['robb.stark@got.com'],
                'readAt' => new \DateTime('2021-01-01 13:45:00'),
                'ip' => '1.2.3.7',
            ],
        ];
    }

    public function getDependencies(): array
    {
        return [
            JobPostingsFixtures::class,
            RecruiterFixtures::class,
        ];
    }
}
