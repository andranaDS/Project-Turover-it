<?php

namespace App\Company\DataFixtures;

use App\Company\Entity\Company;
use App\Company\Entity\CompanyRecruiterFavorite;
use App\Core\DataFixtures\AbstractFixture;
use App\Core\Util\Arrays;
use App\Recruiter\DataFixtures\RecruiterFixtures;
use App\Recruiter\Entity\Recruiter;
use App\User\Entity\User;
use Carbon\Carbon;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CompanyRecruiterFavoritesFixtures extends AbstractFixture implements DependentFixtureInterface
{
    private array $companies = [];
    private array $recruiters = [];

    public function load(ObjectManager $manager): void
    {
        // fetch companies
        foreach ($manager->getRepository(Company::class)->findSome() as $company) {
            /* @var Company $company */
            $this->companies[$company->getSlug()] = $company;
        }

        // fetch recruiters
        foreach ($manager->getRepository(Recruiter::class)->findAll() as $recruiter) {
            /* @var Recruiter $recruiter */
            $this->recruiters[$recruiter->getEmail()] = $recruiter;
        }

        // process data
        foreach ($this->getData() as $data) {
            $favorite = (new CompanyRecruiterFavorite())
                ->setRecruiter($data['recruiter'])
                ->setCompany($data['company'])
                ->setCreatedAt($data['createdAt'])
            ;
            $manager->persist($favorite);
        }

        $manager->flush();
    }

    public function getDevData(): array
    {
        $data = [];
        $now = Carbon::now();

        foreach (Arrays::getRandomSubarray($this->companies, 20, 30) as $company) {
            /* @var Company $company */
            foreach (Arrays::getRandomSubarray($this->recruiters, 1, 5) as $recruiter) {
                /* @var User $user */
                $data[] = [
                    'recruiter' => $recruiter,
                    'company' => $company,
                    'createdAt' => $now,
                ];
            }
        }

        return $data;
    }

    public function getTestData(): array
    {
        return [
            [
                'recruiter' => $this->recruiters['walter.white@breaking-bad.com'],
                'company' => $this->companies['company-1'],
                'createdAt' => new \DateTime('2022-01-01 12:00:00'),
            ],
            [
                'recruiter' => $this->recruiters['walter.white@breaking-bad.com'],
                'company' => $this->companies['company-3'],
                'createdAt' => new \DateTime('2022-01-01 13:00:00'),
            ],
            [
                'recruiter' => $this->recruiters['walter.white@breaking-bad.com'],
                'company' => $this->companies['company-5'],
                'createdAt' => new \DateTime('2022-01-01 14:00:00'),
            ],
            [
                'recruiter' => $this->recruiters['jesse.pinkman@breaking-bad.com'],
                'company' => $this->companies['company-3'],
                'createdAt' => new \DateTime('2022-01-01 12:00:00'),
            ],
            [
                'recruiter' => $this->recruiters['eddard.stark@got.com'],
                'company' => $this->companies['company-3'],
                'createdAt' => new \DateTime('2022-01-01 12:00:00'),
            ],
            [
                'recruiter' => $this->recruiters['robert.ford@ww.com'],
                'company' => $this->companies['company-3'],
                'createdAt' => new \DateTime('2022-01-01 12:00:00'),
            ],
        ];
    }

    public function getDependencies(): array
    {
        return [
            CompaniesFixtures::class,
            RecruiterFixtures::class,
        ];
    }
}
