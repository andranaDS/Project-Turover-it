<?php

namespace App\Resource\DataFixtures;

use App\Core\DataFixtures\AbstractFixture;
use App\Core\DataFixtures\JobFixtures;
use App\Core\Entity\Job;
use App\Core\Util\Arrays;
use App\JobPosting\Enum\Contract;
use App\Resource\Entity\Contribution;
use App\Resource\Enum\Employer;
use App\Resource\Enum\FoundBy;
use App\Resource\Enum\Location;
use App\Resource\Enum\UserCompanyStatus;
use App\User\DataFixtures\UsersFixtures;
use App\User\Entity\User;
use App\User\Enum\ExperienceYear;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ContributionFixtures extends AbstractFixture implements DependentFixtureInterface
{
    public const TOTAL_CONTRIBUTION_BY_JOB = 10000;
    public const FREE_INDEX = 'free';
    public const WORK_INDEX = 'work';
    public const JOB_INDEX = 'job';
    public const CREATED_BY_INDEX = 'createdBy';
    public const USER_COMPANY_STATUS_INDEX = 'userCompanyStatus';
    public const CONTRACT_INDEX = 'contract';
    public const LOCATION_INDEX = 'location';
    public const EXPERIENCE_YEAR_INDEX = 'experienceYear';
    public const EMPLOYER_INDEX = 'employer';
    public const FOUND_BY_INDEX = 'foundBy';
    public const ON_CALL_INDEX = 'onCall';
    public const ANNUAL_SALARY_INDEX = 'annualSalary';
    public const VARIABLE_ANNUAL_SALARY_INDEX = 'variableAnnualSalary';
    public const DAILY_SALARY_INDEX = 'dailySalary';
    public const REMOTE_DAYS_PER_WEEK_INDEX = 'remoteDaysPerWeek';
    public const CONTRACT_DURATION_INDEX = 'contractDuration';
    public const SEARCH_JOB_DURATION_INDEX = 'searchJobDuration';
    public const EXPERIENCE_YEAR_LOCATION = 'experienceYearLocation';

    /** @var User[] */
    private array $users;

    /** @var Job[] */
    private array $jobs;

    public function load(ObjectManager $manager): void
    {
        foreach ($manager->getRepository(User::class)->findAll() as $user) {
            $this->users[$user->getId()] = $user;
        }

        foreach ($manager->getRepository(Job::class)->findAll() as $job) {
            $this->jobs[$job->getId()] = $job;
        }

        $tenDaysAgo = (new \DateTime('now'))->modify('-10 days');

        foreach ($this->getData() as $contractData) {
            $createdAt = $tenDaysAgo->modify('+ 1 second');
            $contribution = (new Contribution())
                ->setJob($contractData[self::JOB_INDEX])
                ->setCreatedBy($contractData[self::CREATED_BY_INDEX])
                ->setUserCompanyStatus($contractData[self::USER_COMPANY_STATUS_INDEX])
                ->setContract($contractData[self::CONTRACT_INDEX])
                ->setLocation($contractData[self::LOCATION_INDEX])
                ->setExperienceYear($contractData[self::EXPERIENCE_YEAR_INDEX])
                ->setEmployer($contractData[self::EMPLOYER_INDEX])
                ->setFoundBy($contractData[self::FOUND_BY_INDEX])
                ->setOnCall($contractData[self::ON_CALL_INDEX])
                ->setAnnualSalary($contractData[self::ANNUAL_SALARY_INDEX])
                ->setVariableAnnualSalary($contractData[self::VARIABLE_ANNUAL_SALARY_INDEX])
                ->setDailySalary($contractData[self::DAILY_SALARY_INDEX])
                ->setRemoteDaysPerWeek($contractData[self::REMOTE_DAYS_PER_WEEK_INDEX])
                ->setContractDuration($contractData[self::CONTRACT_DURATION_INDEX])
                ->setSearchJobDuration($contractData[self::SEARCH_JOB_DURATION_INDEX])
                ->setCreatedAt(clone $createdAt)
            ;

            $manager->persist($contribution);
        }

        $manager->flush();
    }

    public function getTestData(): array
    {
        $testData = [];
        $data = [
            self::WORK_INDEX => [
                self::JOB_INDEX => [
                    self::TOTAL_CONTRIBUTION_BY_JOB => $this->jobs[1],
                ],
                self::CREATED_BY_INDEX => [
                    '3000' => $this->users[6],
                    '6000' => $this->users[7],
                    self::TOTAL_CONTRIBUTION_BY_JOB => $this->users[8],
                ],
                self::EMPLOYER_INDEX => [
                    '3000' => 'final_client',
                    '4500' => 'digital_service_company',
                    '7000' => 'agency',
                    self::TOTAL_CONTRIBUTION_BY_JOB => 'recruitment_agency',
                ],
                self::FOUND_BY_INDEX => [
                    '6000' => 'freework',
                    '8000' => 'directly',
                    self::TOTAL_CONTRIBUTION_BY_JOB => 'intermediary',
                ],
                self::ON_CALL_INDEX => [
                    '4000' => true,
                    self::TOTAL_CONTRIBUTION_BY_JOB => false,
                ],
                self::VARIABLE_ANNUAL_SALARY_INDEX => [
                    '2000' => 0,
                    '6000' => 500,
                    '7000' => 6000,
                    self::TOTAL_CONTRIBUTION_BY_JOB => 10000,
                ],
                self::REMOTE_DAYS_PER_WEEK_INDEX => [
                    '500' => 0,
                    '1500' => 1,
                    '2500' => 2,
                    '6000' => 3,
                    '9000' => 4,
                    self::TOTAL_CONTRIBUTION_BY_JOB => 5,
                ],
                self::CONTRACT_DURATION_INDEX => [
                    '500' => 5,
                    '2800' => 15,
                    '2700' => 30,
                    '6000' => 90,
                    '8000' => 160,
                    '9000' => 200,
                    self::TOTAL_CONTRIBUTION_BY_JOB => 260,
                ],
                self::SEARCH_JOB_DURATION_INDEX => [
                    '500' => 0,
                    '2800' => 1,
                    '2700' => 2,
                    '6000' => 3,
                    '8000' => 4,
                    self::TOTAL_CONTRIBUTION_BY_JOB => 6,
                ],
                self::CONTRACT_INDEX => [
                    '8000' => 'permanent',
                    '9500' => 'fixed-term',
                    '9800' => 'internship',
                    self::TOTAL_CONTRIBUTION_BY_JOB => 'apprenticeship',
                ],
                self::ANNUAL_SALARY_INDEX => [
                    // < 1year
                    ['count' => 500, 'value' => 32000],
                    ['count' => 750, 'value' => 34000],
                    ['count' => 900, 'value' => 35000],
                    ['count' => 1000, 'value' => 37000],
                    // 1-2 years
                    ['count' => 1200, 'value' => 37000],
                    ['count' => 1600, 'value' => 39000],
                    ['count' => 2500, 'value' => 40000],
                    ['count' => 3500, 'value' => 42000],
                    // 3-4 years
                    ['count' => 3600, 'value' => 42000],
                    ['count' => 3700, 'value' => 45000],
                    ['count' => 4200, 'value' => 47000],
                    ['count' => 5200, 'value' => 49000],
                    // 5-10 years
                    ['count' => 5300, 'value' => 49000],
                    ['count' => 5600, 'value' => 52000],
                    ['count' => 6300, 'value' => 53000],
                    ['count' => 7500, 'value' => 55000],
                    // 11-15 years
                    ['count' => 7700, 'value' => 55000],
                    ['count' => 8000, 'value' => 58000],
                    ['count' => 8500, 'value' => 60000],
                    ['count' => 9000, 'value' => 65000],
                    // > 15 years
                    ['count' => 9200, 'value' => 65000],
                    ['count' => 9700, 'value' => 68000],
                    ['count' => 9900, 'value' => 72000],
                    ['count' => self::TOTAL_CONTRIBUTION_BY_JOB, 'value' => 75000],
                ],
                self::EXPERIENCE_YEAR_LOCATION => [
                    ExperienceYear::LESS_THAN_1_YEAR => [
                        500 => Location::ILE_DE_FRANCE,
                        700 => Location::LARGE_CITIES,
                        900 => Location::SMALL_CITIES,
                        1000 => Location::OUTSIDE_FRANCE,
                    ],
                    ExperienceYear::YEARS_1_2 => [
                        1200 => Location::ILE_DE_FRANCE,
                        2500 => Location::LARGE_CITIES,
                        3000 => Location::SMALL_CITIES,
                        3500 => Location::OUTSIDE_FRANCE,
                    ],
                    ExperienceYear::YEARS_3_4 => [
                        4100 => Location::ILE_DE_FRANCE,
                        4500 => Location::LARGE_CITIES,
                        4800 => Location::SMALL_CITIES,
                        5200 => Location::OUTSIDE_FRANCE,
                    ],
                    ExperienceYear::YEARS_5_10 => [
                        6300 => Location::ILE_DE_FRANCE,
                        6500 => Location::LARGE_CITIES,
                        7200 => Location::SMALL_CITIES,
                        7500 => Location::OUTSIDE_FRANCE,
                    ],
                    ExperienceYear::YEARS_11_15 => [
                        7700 => Location::ILE_DE_FRANCE,
                        8200 => Location::LARGE_CITIES,
                        8850 => Location::SMALL_CITIES,
                        9000 => Location::OUTSIDE_FRANCE,
                    ],
                    ExperienceYear::MORE_THAN_15_YEARS => [
                        9200 => Location::ILE_DE_FRANCE,
                        9700 => Location::LARGE_CITIES,
                        9900 => Location::SMALL_CITIES,
                        self::TOTAL_CONTRIBUTION_BY_JOB => Location::OUTSIDE_FRANCE,
                    ],
                ],
            ],
            self::FREE_INDEX => [
                self::JOB_INDEX => [
                    self::TOTAL_CONTRIBUTION_BY_JOB => $this->jobs[1],
                ],
                self::CREATED_BY_INDEX => [
                    '2000' => $this->users[6],
                    '8000' => $this->users[7],
                    self::TOTAL_CONTRIBUTION_BY_JOB => $this->users[8],
                ],

                self::EMPLOYER_INDEX => [
                    '1500' => 'final_client',
                    '3600' => 'digital_service_company',
                    '6500' => 'agency',
                    self::TOTAL_CONTRIBUTION_BY_JOB => 'recruitment_agency',
                ],
                self::FOUND_BY_INDEX => [
                    '3000' => 'freework',
                    '9000' => 'directly',
                    self::TOTAL_CONTRIBUTION_BY_JOB => 'intermediary',
                ],
                self::ON_CALL_INDEX => [
                    '6500' => true,
                    self::TOTAL_CONTRIBUTION_BY_JOB => false,
                ],
                self::REMOTE_DAYS_PER_WEEK_INDEX => [
                    '1500' => 0,
                    '1900' => 1,
                    '4500' => 2,
                    '7000' => 3,
                    '8800' => 4,
                    self::TOTAL_CONTRIBUTION_BY_JOB => 5,
                ],
                self::CONTRACT_DURATION_INDEX => [
                    '600' => 5,
                    '3500' => 15,
                    '7500' => 30,
                    '8000' => 90,
                    '8800' => 160,
                    '9500' => 200,
                    self::TOTAL_CONTRIBUTION_BY_JOB => 260,
                ],
                self::SEARCH_JOB_DURATION_INDEX => [
                    '600' => 0,
                    '1200' => 1,
                    '2200' => 2,
                    '3000' => 3,
                    '8500' => 4,
                    self::TOTAL_CONTRIBUTION_BY_JOB => 6,
                ],
                self::CONTRACT_INDEX => [
                    self::TOTAL_CONTRIBUTION_BY_JOB => 'contractor',
                ],
                self::EXPERIENCE_YEAR_LOCATION => [
                    ExperienceYear::LESS_THAN_1_YEAR => [
                        600 => Location::ILE_DE_FRANCE,
                        800 => Location::LARGE_CITIES,
                        1100 => Location::SMALL_CITIES,
                        1200 => Location::OUTSIDE_FRANCE,
                    ],
                    ExperienceYear::YEARS_1_2 => [
                        1300 => Location::ILE_DE_FRANCE,
                        1800 => Location::LARGE_CITIES,
                        2600 => Location::SMALL_CITIES,
                        3100 => Location::OUTSIDE_FRANCE,
                    ],
                    ExperienceYear::YEARS_3_4 => [
                        3800 => Location::ILE_DE_FRANCE,
                        4200 => Location::LARGE_CITIES,
                        4500 => Location::SMALL_CITIES,
                        5100 => Location::OUTSIDE_FRANCE,
                    ],
                    ExperienceYear::YEARS_5_10 => [
                        5900 => Location::ILE_DE_FRANCE,
                        6200 => Location::LARGE_CITIES,
                        6500 => Location::SMALL_CITIES,
                        7300 => Location::OUTSIDE_FRANCE,
                    ],
                    ExperienceYear::YEARS_11_15 => [
                        8100 => Location::ILE_DE_FRANCE,
                        8600 => Location::LARGE_CITIES,
                        9000 => Location::SMALL_CITIES,
                        9200 => Location::OUTSIDE_FRANCE,
                    ],
                    ExperienceYear::MORE_THAN_15_YEARS => [
                        9300 => Location::ILE_DE_FRANCE,
                        9600 => Location::LARGE_CITIES,
                        9700 => Location::SMALL_CITIES,
                        self::TOTAL_CONTRIBUTION_BY_JOB => Location::OUTSIDE_FRANCE,
                    ],
                ],
                self::DAILY_SALARY_INDEX => [
                    // < 1year
                    ['count' => 500, 'value' => 250],
                    ['count' => 600, 'value' => 275],
                    ['count' => 700, 'value' => 280],
                    ['count' => 1000, 'value' => 300],
                    // 1-2 years
                    ['count' => 1200, 'value' => 300],
                    ['count' => 1600, 'value' => 325],
                    ['count' => 2500, 'value' => 350],
                    ['count' => 3500, 'value' => 375],
                    // 3-4 years
                    ['count' => 3600, 'value' => 375],
                    ['count' => 3700, 'value' => 410],
                    ['count' => 4200, 'value' => 450],
                    ['count' => 5200, 'value' => 500],
                    // 5-10 years
                    ['count' => 5300, 'value' => 500],
                    ['count' => 5600, 'value' => 520],
                    ['count' => 6300, 'value' => 575],
                    ['count' => 7500, 'value' => 620],
                    // 11-15 years
                    ['count' => 7700, 'value' => 620],
                    ['count' => 8000, 'value' => 650],
                    ['count' => 8500, 'value' => 700],
                    ['count' => 9000, 'value' => 725],
                    // > 15 years
                    ['count' => 9200, 'value' => 725],
                    ['count' => 9500, 'value' => 800],
                    ['count' => 9700, 'value' => 900],
                    ['count' => self::TOTAL_CONTRIBUTION_BY_JOB, 'value' => 1000],
                ],
                self::USER_COMPANY_STATUS_INDEX => [
                    '3500' => 'company',
                    '4500' => 'micro_company',
                    self::TOTAL_CONTRIBUTION_BY_JOB => 'salary_portage',
                ],
            ],
        ];

        foreach ($data as $contractData) {
            $contractData = array_merge($this->getContractDefaultData(), $contractData);

            for ($i = 0; $i < self::TOTAL_CONTRIBUTION_BY_JOB; ++$i) {
                $testData[] = $this->getContributionData($contractData, $i);
            }
        }

        return $testData;
    }

    public function getContractDefaultData(): array
    {
        return [
            self::JOB_INDEX => [
                self::JOB_INDEX => null,
            ],
            self::CREATED_BY_INDEX => [
                self::TOTAL_CONTRIBUTION_BY_JOB => null,
            ],
            self::LOCATION_INDEX => [
                self::TOTAL_CONTRIBUTION_BY_JOB => null,
            ],
            self::EMPLOYER_INDEX => [
                self::TOTAL_CONTRIBUTION_BY_JOB => null,
            ],
            self::FOUND_BY_INDEX => [
                self::TOTAL_CONTRIBUTION_BY_JOB => null,
            ],
            self::ON_CALL_INDEX => [
                self::TOTAL_CONTRIBUTION_BY_JOB => null,
            ],
            self::VARIABLE_ANNUAL_SALARY_INDEX => [
                self::TOTAL_CONTRIBUTION_BY_JOB => null,
            ],
            self::USER_COMPANY_STATUS_INDEX => [
                self::TOTAL_CONTRIBUTION_BY_JOB => null,
            ],
            self::REMOTE_DAYS_PER_WEEK_INDEX => [
                self::TOTAL_CONTRIBUTION_BY_JOB => null,
            ],
            self::CONTRACT_DURATION_INDEX => [
                self::TOTAL_CONTRIBUTION_BY_JOB => null,
            ],
            self::SEARCH_JOB_DURATION_INDEX => [
                self::TOTAL_CONTRIBUTION_BY_JOB => null,
            ],
            self::ANNUAL_SALARY_INDEX => [
                self::TOTAL_CONTRIBUTION_BY_JOB => null,
            ],
            self::DAILY_SALARY_INDEX => [
                self::TOTAL_CONTRIBUTION_BY_JOB => null,
            ],
            self::EXPERIENCE_YEAR_INDEX => [
                self::TOTAL_CONTRIBUTION_BY_JOB => null,
            ],
            self::CONTRACT_INDEX => [
                self::TOTAL_CONTRIBUTION_BY_JOB => null,
            ],
        ];
    }

    private function getContributionData(array $data, int $index): array
    {
        return array_merge(
            [
                self::SEARCH_JOB_DURATION_INDEX => $this->getValueFromConfig($data[self::SEARCH_JOB_DURATION_INDEX], $index, self::SEARCH_JOB_DURATION_INDEX),
                self::VARIABLE_ANNUAL_SALARY_INDEX => $this->getValueFromConfig($data[self::VARIABLE_ANNUAL_SALARY_INDEX], $index, self::VARIABLE_ANNUAL_SALARY_INDEX),
                self::USER_COMPANY_STATUS_INDEX => $this->getValueFromConfig($data[self::USER_COMPANY_STATUS_INDEX], $index, self::USER_COMPANY_STATUS_INDEX),
                self::REMOTE_DAYS_PER_WEEK_INDEX => $this->getValueFromConfig($data[self::REMOTE_DAYS_PER_WEEK_INDEX], $index, self::REMOTE_DAYS_PER_WEEK_INDEX),
                self::CONTRACT_INDEX => $this->getValueFromConfig($data[self::CONTRACT_INDEX], $index, self::CONTRACT_INDEX),
                self::EMPLOYER_INDEX => $this->getValueFromConfig($data[self::EMPLOYER_INDEX], $index, self::EMPLOYER_INDEX),
                self::FOUND_BY_INDEX => $this->getValueFromConfig($data[self::FOUND_BY_INDEX], $index, self::FOUND_BY_INDEX),
                self::ON_CALL_INDEX => $this->getValueFromConfig($data[self::ON_CALL_INDEX], $index, self::ON_CALL_INDEX),
                self::ANNUAL_SALARY_INDEX => $this->getValueFromConfig($data[self::ANNUAL_SALARY_INDEX], $index, self::ANNUAL_SALARY_INDEX),
                self::DAILY_SALARY_INDEX => $this->getValueFromConfig($data[self::DAILY_SALARY_INDEX], $index, self::DAILY_SALARY_INDEX),
                self::CONTRACT_DURATION_INDEX => $this->getValueFromConfig($data[self::CONTRACT_DURATION_INDEX], $index, self::CONTRACT_DURATION_INDEX),
                self::CREATED_BY_INDEX => $this->getValueFromConfig($data[self::CREATED_BY_INDEX], $index, self::CREATED_BY_INDEX),
                self::JOB_INDEX => $this->getValueFromConfig($data[self::JOB_INDEX], $index, self::JOB_INDEX),
            ],
            $this->getExperienceYearLocationValue($data[self::EXPERIENCE_YEAR_LOCATION], $index)
        );
    }

    // @phpstan-ignore-next-line
    private function getValueFromConfig(array $data, int $index, string $field)
    {
        foreach ($data as $key => $datum) {
            if (\is_array($datum)) {
                if ($index < $datum['count']) {
                    return $datum['value'];
                }
            } elseif ($index < $key) {
                return $datum;
            }
        }

        throw new \DomainException('problem concerning the field ' . $field);
    }

    private function getExperienceYearLocationValue(array $data, int $index): array
    {
        foreach ($data as $experienceYearLabel => $experienceYearData) {
            foreach ($experienceYearData as $count => $value) {
                if ($index < $count) {
                    return [
                        self::EXPERIENCE_YEAR_INDEX => $experienceYearLabel,
                        self::LOCATION_INDEX => $value,
                    ];
                }
            }
        }

        throw new \DomainException('problem concerning the field experienceYearLocation');
    }

    public function getDevData(): array
    {
        $data = [];

        foreach ($this->jobs as $job) {
            $contributionsCount = random_int(15, 50);
            for ($i = 0; $i < $contributionsCount; ++$i) {
                $contract = 0 === $i % 2 ? Arrays::getRandom(Contract::getWorkValues()) : Arrays::getRandom(Contract::getFreeValues());
                $data[] = [
                    self::SEARCH_JOB_DURATION_INDEX => random_int(0, 10),
                    self::VARIABLE_ANNUAL_SALARY_INDEX => random_int(0, 25000),
                    self::USER_COMPANY_STATUS_INDEX => Arrays::getRandom(UserCompanyStatus::getConstants()),
                    self::REMOTE_DAYS_PER_WEEK_INDEX => random_int(0, 5),
                    self::CONTRACT_INDEX => $contract,
                    self::EMPLOYER_INDEX => Arrays::getRandom(Employer::getConstants()),
                    self::FOUND_BY_INDEX => Arrays::getRandom(FoundBy::getConstants()),
                    self::ON_CALL_INDEX => 1 === random_int(0, 1),
                    self::ANNUAL_SALARY_INDEX => random_int(32000, 65000),
                    self::DAILY_SALARY_INDEX => random_int(250, 900),
                    self::CONTRACT_DURATION_INDEX => random_int(0, 22),
                    self::CREATED_BY_INDEX => $this->users[random_int(1, 3)],
                    self::JOB_INDEX => $job,
                    self::LOCATION_INDEX => Arrays::getRandom(Location::getConstants()),
                    self::EXPERIENCE_YEAR_INDEX => Arrays::getRandom(ExperienceYear::getConstants()),
                ];
            }
        }

        return $data;
    }

    public function getDependencies(): array
    {
        return [
            JobFixtures::class,
            UsersFixtures::class,
        ];
    }
}
