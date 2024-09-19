<?php

namespace App\JobPosting\DataFixtures;

use App\Company\Entity\Company;
use App\Core\DataFixtures\AbstractFixture;
use App\Core\Util\Arrays;
use App\JobPosting\Entity\Application;
use App\JobPosting\Entity\JobPosting;
use App\JobPosting\Enum\ApplicationStep;
use App\User\DataFixtures\UsersFixtures;
use App\User\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;

class ApplicationFixtures extends AbstractFixture implements DependentFixtureInterface
{
    private array $users = [];
    private array $jobs = [];
    private array $companies = [];

    public function load(ObjectManager $manager): void
    {
        // fetch users
        foreach ($manager->getRepository(User::class)->findAll() as $user) {
            /* @var User $user */
            if ($user->getProfileJobTitle()) {
                $this->users[$user->getEmail()] = $user;
            }
        }

        // fetch companies
        foreach ($manager->getRepository(Company::class)->findSome() as $company) {
            /* @var Company $company */
            if (null !== $companyData = $company->getData()) {
                if ($companyData->getJobPostingsWorkTotalCount() > 0) {
                    $this->companies[$company->getName()] = $company;
                }
            }
        }

        // fetch all jobs
        foreach ($manager->getRepository(JobPosting::class)->findAll() as $jobPosting) {
            /* @var JobPosting $jobPosting */
            $this->jobs[$jobPosting->getTitle()] = $jobPosting;
        }

        foreach ($this->getData() as $d) {
            $application = (new Application())
                ->setStep($d['step'])
                ->setContent($d['content'] ?? null)
                ->setSeenAt($d['seenAt'] ?? null)
                ->setUser($d['user'])
                ->setJobPosting($d['jobPosting'] ?? null)
                ->setCompany($d['company'] ?? null)
                ->setCreatedAt($d['createdAt'] ?? null)
                ->setUpdatedAt($d['updatedAt'] ?? null)
            ;
            $manager->persist($application);
        }

        $manager->flush();
    }

    public function getDevData(): array
    {
        $faker = Faker::create('fr_FR');

        $data = [];

        foreach ($this->users as $user) {
            if (0 !== mt_rand(0, 4) || 1 === $user->getId()) {
                $applicationsCount = mt_rand(6, 10);
                for ($i = 0; $i < $applicationsCount; ++$i) {
                    $jobPosting = null;
                    $company = null;
                    $step = Arrays::getRandom(ApplicationStep::getConstants());
                    $createdAt = $faker->dateTimeBetween('- 8 months', '- 6 month');
                    $updatedAt = $faker->dateTimeBetween('- 6 months', '- 1 month');
                    $seenAt = $faker->dateTimeBetween('- 3 months', '- 2 months');

                    if (0 !== mt_rand(0, 4)) {
                        $jobPosting = Arrays::getRandom($this->jobs);
                    } else {
                        $company = Arrays::getRandom($this->companies);
                    }

                    $data[] = [
                        'user' => $user,
                        'jobPosting' => $jobPosting ?? null,
                        'company' => $company ?? null,
                        'content' => mt_rand(0, 1) ? $faker->text(mt_rand(300, 600)) : null,
                        'step' => $step,
                        'createdAt' => $createdAt,
                        'updatedAt' => $updatedAt,
                        'seenAt' => \in_array($step, [ApplicationStep::SEEN, ApplicationStep::KO], true) ? $seenAt : null,
                    ];
                }
            }
        }

        return $data;
    }

    public function getTestData(): array
    {
        return [
            [
                'user' => $this->users['claude.monet@free-work.fr'],
                'jobPosting' => $this->jobs['Responsable applicatifs Finance (H/F) (CDI)'],
                'content' => 'Job 1 - Application 1',
                'step' => ApplicationStep::RESUME,
                'createdAt' => new \DateTime('2021-01-01 10:00:00'),
                'updatedAt' => new \DateTime('2021-01-02 10:00:00'),
            ],
            [
                'user' => $this->users['claude.monet@free-work.fr'],
                'jobPosting' => $this->jobs['Responsable cybersécurité (sans management) (H/F)'],
                'content' => 'Job 2 - Application 1',
                'step' => ApplicationStep::SEEN,
                'seenAt' => new \DateTime('2021-01-02 11:30:00'),
                'createdAt' => new \DateTime('2021-01-02 10:00:00'),
                'updatedAt' => new \DateTime('2021-01-03 10:00:00'),
            ],
            [
                'user' => $this->users['claude.monet@free-work.fr'],
                'jobPosting' => $this->jobs['Ingénieur BI (F/H)'],
                'content' => 'Job 3 - Application 1',
                'step' => ApplicationStep::SEEN,
                'seenAt' => new \DateTime('2021-01-03 12:00:00'),
                'createdAt' => new \DateTime('2021-01-03 10:00:00'),
                'updatedAt' => new \DateTime('2021-01-04 10:00:00'),
            ],
            [
                'user' => $this->users['vincent.van-gogh@free-work.fr'],
                'jobPosting' => $this->jobs['Responsable applicatifs Finance (H/F) (CDI)'],
                'content' => 'Job 1 - Application 2',
                'step' => ApplicationStep::KO,
                'seenAt' => new \DateTime('2021-01-04 12:00:00'),
                'createdAt' => new \DateTime('2021-01-04 10:00:00'),
                'updatedAt' => new \DateTime('2021-01-05 10:00:00'),
            ],
            [
                'user' => $this->users['vincent.van-gogh@free-work.fr'],
                'jobPosting' => $this->jobs['Responsable cybersécurité (sans management) (H/F)'],
                'content' => 'Job 2 - Application 2',
                'step' => ApplicationStep::SEEN,
                'seenAt' => new \DateTime('2021-01-05 12:00:00'),
                'createdAt' => new \DateTime('2021-01-05 10:00:00'),
                'updatedAt' => new \DateTime('2021-01-06 10:00:00'),
            ],
            [
                'user' => $this->users['auguste.renoir@free-work.fr'],
                'company' => $this->companies['Company 1'],
                'content' => 'Company 1 - Application 1',
                'step' => ApplicationStep::SEEN,
                'seenAt' => new \DateTime('2021-01-06 12:00:00'),
                'createdAt' => new \DateTime('2021-01-06 10:00:00'),
                'updatedAt' => new \DateTime('2021-01-07 10:00:00'),
            ],
            [
                'user' => $this->users['auguste.renoir@free-work.fr'],
                'company' => $this->companies['Company 2'],
                'content' => 'Company 2 - Application 1',
                'step' => ApplicationStep::RESUME,
                'createdAt' => new \DateTime('2021-01-07 10:00:00'),
                'updatedAt' => new \DateTime('2021-01-08 10:00:00'),
            ],
        ];
    }

    public static function getGroups(): array
    {
        return ['application']; /* @phpstan-ignore-line */
    }

    public function getDependencies()
    {
        return [
            JobPostingsFixtures::class,
            JobPostingUserFavoritesFixtures::class,
            UsersFixtures::class,
        ];
    }
}
