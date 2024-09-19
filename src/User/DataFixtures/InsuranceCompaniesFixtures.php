<?php

namespace App\User\DataFixtures;

use App\Core\DataFixtures\AbstractFixture;
use App\Core\Enum\Locale;
use App\User\Entity\InsuranceCompany;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Yaml\Yaml;

class InsuranceCompaniesFixtures extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getData() as $d) {
            $insuranceCompany = (new InsuranceCompany())
                ->setName($d['name'])
                ->setLocales($d['locales'])
            ;
            $manager->persist($insuranceCompany);
        }

        $manager->flush();
    }

    public function getDevData(): array
    {
        $umbrellaCompanies = Yaml::parseFile(__DIR__ . '/Data/insurance_company.yaml');

        return $this->prepareInsuranceCompanies($umbrellaCompanies);
    }

    public function getTestData(): array
    {
        return [
            [
                'name' => 'Insurance Company 1',
                'locales' => [
                    Locale::fr_FR,
                    Locale::fr_BE,
                    Locale::fr_CH,
                    Locale::fr_LU,
                ],
            ],
            [
                'name' => 'Insurance Company 2',
                'locales' => [
                    Locale::fr_FR,
                    Locale::fr_BE,
                    Locale::fr_CH,
                    Locale::fr_LU,
                ],
            ],
            [
                'name' => 'Insurance Company 3',
                'locales' => [
                    Locale::fr_FR,
                    Locale::fr_BE,
                    Locale::fr_CH,
                    Locale::fr_LU,
                ],
            ],
            [
                'name' => 'Insurance Company 4',
                'locales' => [
                    Locale::fr_FR,
                    Locale::fr_BE,
                    Locale::fr_CH,
                    Locale::fr_LU,
                ],
            ],
            [
                'name' => 'Insurance Company 5 search',
                'locales' => [
                    Locale::fr_FR,
                    Locale::fr_BE,
                    Locale::fr_CH,
                    Locale::fr_LU,
                ],
            ],
        ];
    }

    private function prepareInsuranceCompanies(array $insuranceCompanies): array
    {
        foreach ($insuranceCompanies as &$insuranceCompany) {
            $insuranceCompany['locales'] = [
                Locale::fr_FR,
                Locale::fr_BE,
                Locale::fr_CH,
                Locale::fr_LU,
            ];
        }

        return $insuranceCompanies;
    }

    public static function getGroups(): array
    {
        return ['user'];
    }
}
