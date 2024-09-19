<?php

namespace App\Company\DataFixtures;

use App\Company\Entity\Company;
use App\Company\Entity\CompanyUserFavorite;
use App\Core\DataFixtures\AbstractFixture;
use App\Core\Util\Arrays;
use App\User\DataFixtures\UsersFixtures;
use App\User\Entity\User;
use Carbon\Carbon;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CompanyUserFavoritesFixtures extends AbstractFixture implements DependentFixtureInterface
{
    private array $companies = [];
    private array $users = [];

    public function load(ObjectManager $manager): void
    {
        // fetch companies
        foreach ($manager->getRepository(Company::class)->findSome() as $company) {
            /* @var Company $company */
            $this->companies[$company->getSlug()] = $company;
        }

        // fetch users
        foreach ($manager->getRepository(User::class)->findAll() as $user) {
            /* @var User $user */
            $this->users[$user->getEmail()] = $user;
        }

        // process data
        foreach ($this->getData() as $data) {
            $favorite = (new CompanyUserFavorite())
                ->setUser($data['user'])
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
            foreach (Arrays::getRandomSubarray($this->users, 1, 5) as $user) {
                /* @var User $user */
                $data[] = [
                    'user' => $user,
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
                'user' => $this->users['claude.monet@free-work.fr'],
                'company' => $this->companies['company-1'],
                'createdAt' => new \DateTime('2021-01-01 12:00:00'),
            ],
            [
                'user' => $this->users['claude.monet@free-work.fr'],
                'company' => $this->companies['company-3'],
                'createdAt' => new \DateTime('2021-01-01 13:00:00'),
            ],
            [
                'user' => $this->users['claude.monet@free-work.fr'],
                'company' => $this->companies['company-5'],
                'createdAt' => new \DateTime('2021-01-01 14:00:00'),
            ],
            [
                'user' => $this->users['vincent.van-gogh@free-work.fr'],
                'company' => $this->companies['company-3'],
                'createdAt' => new \DateTime('2021-01-01 12:00:00'),
            ],
            [
                'user' => $this->users['auguste.renoir@free-work.fr'],
                'company' => $this->companies['company-3'],
                'createdAt' => new \DateTime('2021-01-01 12:00:00'),
            ],
            [
                'user' => $this->users['henri.matisse@free-work.fr'],
                'company' => $this->companies['company-3'],
                'createdAt' => new \DateTime('2021-01-01 12:00:00'),
            ],
        ];
    }

    public function getDependencies(): array
    {
        return [
            CompaniesFixtures::class,
            UsersFixtures::class,
        ];
    }
}
