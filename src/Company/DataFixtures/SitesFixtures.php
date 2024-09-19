<?php

namespace App\Company\DataFixtures;

use App\Company\Entity\Company;
use App\Company\Entity\Site;
use App\Core\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;

class SitesFixtures extends AbstractFixture implements DependentFixtureInterface
{
    private array $companies = [];

    public function load(ObjectManager $manager): void
    {
        // fetch companies
        foreach ($manager->getRepository(Company::class)->findAll() as $company) {
            /* @var Company $company */
            $this->companies[$company->getSlug()] = $company;
        }

        foreach ($this->getData() as $d) {
            $site = (new Site())
                ->setName(ucwords($d['name']))
                ->setIp($d['ip'])
                ->setCompany($d['company'])
                ->setCreatedAt($d['createdAt'] ?? null)
                ->setUpdatedAt($d['updatedAt'] ?? null)
            ;

            $manager->persist($site);
        }

        $manager->flush();
        $manager->clear();
    }

    public function getDevData(): array
    {
        $data = [];
        $faker = Faker::create('fr_FR');

        foreach ($this->companies as $company) {
            for ($i = 0; $i <= mt_rand(0, 4); ++$i) {
                $createdAt = $faker->dateTimeBetween('- 12 months');
                $data[] = [
                    'name' => $faker->word,
                    'ip' => $faker->ipv4(),
                    'createdAt' => $createdAt,
                    'updatedAt' => $createdAt,
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
                'name' => 'Site 1 - Company 1',
                'ip' => '1.1.1.1',
                'createdAt' => new \DateTime('2022-01-01 10:00:00'),
                'updatedAt' => new \DateTime('2022-01-01 10:30:00'),
                'company' => $this->companies['company-1'],
            ],
            [
                'name' => 'Site 2  - Company 1',
                'ip' => '1.1.1.2',
                'createdAt' => new \DateTime('2022-01-01 11:00:00'),
                'updatedAt' => new \DateTime('2022-01-01 11:30:00'),
                'company' => $this->companies['company-1'],
            ],
            [
                'name' => 'Site 1 - Company 2',
                'ip' => '1.1.2.1',
                'createdAt' => new \DateTime('2022-01-02 10:00:00'),
                'updatedAt' => new \DateTime('2022-01-02 10:30:00'),
                'company' => $this->companies['company-2'],
            ],
            [
                'name' => 'Site 2 - Company 2',
                'ip' => '1.1.2.2',
                'createdAt' => new \DateTime('2022-01-02 11:00:00'),
                'updatedAt' => new \DateTime('2022-01-02 11:30:00'),
                'company' => $this->companies['company-2'],
            ],
            [
                'name' => 'Site 3 - Company 2',
                'ip' => '1.1.2.3',
                'createdAt' => new \DateTime('2022-01-02 12:00:00'),
                'updatedAt' => new \DateTime('2022-01-02 12:30:00'),
                'company' => $this->companies['company-2'],
            ],
            [
                'name' => 'Site 1 - Company 3',
                'ip' => '1.1.3.1',
                'createdAt' => new \DateTime('2022-01-03 12:00:00'),
                'updatedAt' => new \DateTime('2022-01-03 12:30:00'),
                'company' => $this->companies['company-3'],
            ],
            [
                'name' => 'Site 1 - Company 4',
                'ip' => '1.1.4.1',
                'createdAt' => new \DateTime('2022-01-04 12:00:00'),
                'updatedAt' => new \DateTime('2022-01-04 12:30:00'),
                'company' => $this->companies['company-4'],
            ],
        ];
    }

    public function getDependencies(): array
    {
        return [
            CompaniesFixtures::class,
        ];
    }
}
