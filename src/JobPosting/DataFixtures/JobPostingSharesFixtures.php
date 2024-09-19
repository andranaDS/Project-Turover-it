<?php

namespace App\JobPosting\DataFixtures;

use App\Core\DataFixtures\AbstractFixture;
use App\Core\Util\Arrays;
use App\JobPosting\Entity\JobPosting;
use App\JobPosting\Entity\JobPostingShare;
use App\Recruiter\Entity\Recruiter;
use Carbon\Carbon;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class JobPostingSharesFixtures extends AbstractFixture implements DependentFixtureInterface
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
            $share = (new JobPostingShare())
                ->setJobPosting($d['jobPosting'])
                ->setSharedBy($d['sharedBy'])
                ->setEmail($d['recruiter']->getEmail())
                ->setCreatedAt($d['createdAt'])
            ;

            $manager->persist($share);
        }

        $manager->flush();
    }

    public function getDevData(): array
    {
        $data = [];

        $sharesCount = 1000;
        for ($i = 0; $i < $sharesCount; ++$i) {
            $data[] = [
                'jobPosting' => Arrays::getRandom($this->jobPostings),
                'sharedBy' => Arrays::getRandom($this->recruiters),
                'recruiter' => Arrays::getRandom($this->recruiters),
                'createdAt' => Carbon::now(),
            ];
        }

        return $data;
    }

    public function getTestData(): array
    {
        return [
            [
                'jobPosting' => $this->jobPostings[1],
                'sharedBy' => $this->recruiters['robb.stark@got.com'],
                'recruiter' => $this->recruiters['bernard.lowe@ww.com'],
                'createdAt' => new \DateTime('2022-03-01 23:30:00'),
            ],
            [
                'jobPosting' => $this->jobPostings[2],
                'sharedBy' => $this->recruiters['jesse.pinkman@breaking-bad.com'],
                'recruiter' => $this->recruiters['teddy.flood@ww.com'],
                'createdAt' => new \DateTime('2022-03-01 23:45:00'),
            ],
            [
                'jobPosting' => $this->jobPostings[3],
                'sharedBy' => $this->recruiters['walter.white@breaking-bad.com'],
                'recruiter' => $this->recruiters['carrie.mathison@homeland.com'],
                'createdAt' => new \DateTime('2022-03-01 23:30:00'),
            ],
            [
                'jobPosting' => $this->jobPostings[4],
                'sharedBy' => $this->recruiters['robb.stark@got.com'],
                'recruiter' => $this->recruiters['peter.quinn@homeland.com'],
                'createdAt' => new \DateTime('2022-03-01 23:45:00'),
            ],
            [
                'jobPosting' => $this->jobPostings[5],
                'sharedBy' => $this->recruiters['walter.white@breaking-bad.com'],
                'recruiter' => $this->recruiters['guillaume.debailly@le-bureau-des-legendes.fr'],
                'createdAt' => new \DateTime('2022-03-01 23:30:00'),
            ],
            [
                'jobPosting' => $this->jobPostings[6],
                'sharedBy' => $this->recruiters['walter.white@breaking-bad.com'],
                'recruiter' => $this->recruiters['henri.duflot@le-bureau-des-legendes.fr'],
                'createdAt' => new \DateTime('2022-03-01 23:45:00'),
            ],
        ];
    }

    public function getDependencies()
    {
        return [
            JobPostingTemplatesFixtures::class,
        ];
    }
}
