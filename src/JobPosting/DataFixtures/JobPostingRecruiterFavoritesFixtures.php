<?php

namespace App\JobPosting\DataFixtures;

use App\Core\DataFixtures\AbstractFixture;
use App\Core\Util\Arrays;
use App\JobPosting\Entity\JobPosting;
use App\JobPosting\Entity\JobPostingRecruiterFavorite;
use App\Recruiter\DataFixtures\RecruiterFixtures;
use App\Recruiter\Entity\Recruiter;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class JobPostingRecruiterFavoritesFixtures extends AbstractFixture implements DependentFixtureInterface
{
    private array $recruiters;
    private array $jobs;

    public function load(ObjectManager $manager): void
    {
        // fetch all recruiters
        foreach ($manager->getRepository(Recruiter::class)->findAll() as $recruiter) {
            /* @var Recruiter $recruiter */
            $this->recruiters[$recruiter->getEmail()] = $recruiter;
        }

        // fetch all jobs
        foreach ($manager->getRepository(JobPosting::class)->findAll() as $jobPosting) {
            /* @var JobPosting $jobPosting */
            $this->jobs[$jobPosting->getTitle()] = $jobPosting;
        }

        // process data
        foreach ($this->getData() as $d) {
            $jobPostingRecruiterFavorite = (new JobPostingRecruiterFavorite())
                ->setJobPosting($d['jobPosting'])
                ->setRecruiter($d['recruiter'])
                ->setCreatedAt($d['createdAt'])
            ;

            $manager->persist($jobPostingRecruiterFavorite);
        }

        $manager->flush();
    }

    public function getDevData(): array
    {
        $data = [];
        $faker = Factory::create('fr_Fr');

        foreach ($this->jobs as $job) {
            /** @var JobPosting $job */
            $jobFavoritesRecruiters = Arrays::getRandomSubarray($this->recruiters, 0, 16);
            foreach ($jobFavoritesRecruiters as $jobFavoritesRecruiter) {
                /* @var Recruiter $jobFavoritesRecruiter */

                $data[] = [
                    'jobPosting' => $job,
                    'recruiter' => $jobFavoritesRecruiter,
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
                'recruiter' => $this->recruiters['walter.white@breaking-bad.com'],
                'createdAt' => new \DateTime('2021-03-01 23:30:00'),
            ],
            [
                'jobPosting' => $this->jobs['Responsable applicatifs Finance (H/F) (CDI)'],
                'recruiter' => $this->recruiters['jesse.pinkman@breaking-bad.com'],
                'createdAt' => new \DateTime('2021-03-01 23:45:00'),
            ],
        ];
    }

    public function getDependencies()
    {
        return [
            RecruiterFixtures::class,
            JobPostingsFixtures::class,
        ];
    }
}
