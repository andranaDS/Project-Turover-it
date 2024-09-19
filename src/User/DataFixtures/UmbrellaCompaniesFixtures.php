<?php

namespace App\User\DataFixtures;

use App\Core\DataFixtures\AbstractFixture;
use App\User\Entity\UmbrellaCompany;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Yaml\Yaml;

class UmbrellaCompaniesFixtures extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getData() as $d) {
            $umbrellaCompany = (new UmbrellaCompany())
                ->setName($d['name'])
                ->setProfileUsageCount($d['profileUsageCount'])
            ;
            $manager->persist($umbrellaCompany);
        }

        $manager->flush();
    }

    public function getDevData(): array
    {
        $umbrellaCompanies = Yaml::parseFile(__DIR__ . '/Data/umbrella_company_fr.yaml');

        return $this->prepareUmbrellaCompanies($umbrellaCompanies);
    }

    public function getTestData(): array
    {
        return [
            [
                'name' => 'Umbrella Company 1',
                'profileUsageCount' => 15,
            ],
            [
                'name' => 'Umbrella Company 2',
                'profileUsageCount' => 10,
            ],
            [
                'name' => 'Umbrella Company 3',
                'profileUsageCount' => 5,
            ],
            [
                'name' => 'Umbrella Company 4',
                'profileUsageCount' => 1,
            ],
            [
                'name' => 'Umbrella Company 5 search',
                'profileUsageCount' => 1,
            ],
        ];
    }

    private function prepareUmbrellaCompanies(array $umbrellaCompanies): array
    {
        foreach ($umbrellaCompanies as &$umbrellaCompany) {
            $umbrellaCompany['profileUsageCount'] = 10;
        }

        return $umbrellaCompanies;
    }

    public static function getGroups(): array
    {
        return ['user'];
    }
}
