<?php

namespace App\Company\DataFixtures;

use App\Company\Entity\Company;
use App\Company\Entity\CompanyBlacklist;
use App\Core\DataFixtures\AbstractFixture;
use App\Core\Util\Arrays;
use App\User\DataFixtures\UsersFixtures;
use App\User\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CompaniesBlacklistsFixtures extends AbstractFixture implements DependentFixtureInterface
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
            $favorite = (new CompanyBlacklist())
                ->setUser($data['user'])
                ->setCompany($data['company'])
            ;
            $manager->persist($favorite);
        }

        $manager->flush();
    }

    public function getDevData(): array
    {
        $data = [];

        foreach (Arrays::getRandomSubarray($this->companies, 20, 30) as $company) {
            /* @var Company $company */
            foreach (Arrays::getRandomSubarray($this->users, 1, 5) as $user) {
                /* @var User $user */
                $data[] = [
                    'user' => $user,
                    'company' => $company,
                ];
            }
        }

        return $data;
    }

    public function getTestData(): array
    {
        return [
            [
                'user' => $this->users['pablo.picasso@free-work.fr'],
                'company' => $this->companies['company-2'],
            ],
            [
                'user' => $this->users['pablo.picasso@free-work.fr'],
                'company' => $this->companies['company-4'],
            ],
            [
                'user' => $this->users['pablo.picasso@free-work.fr'],
                'company' => $this->companies['company-5'],
            ],
            [
                'user' => $this->users['henri.matisse@free-work.fr'],
                'company' => $this->companies['company-2'],
            ],
            [
                'user' => $this->users['elisabeth.vigee-le-brun@free-work.fr'],
                'company' => $this->companies['company-2'],
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
