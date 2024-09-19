<?php

namespace App\Resource\DataFixtures;

use App\Core\DataFixtures\AbstractFixture;
use App\Core\DataFixtures\JobFixtures;
use App\Core\Entity\Job;
use App\Resource\Entity\JobContributionStatistics;
use App\Resource\Enum\Employer;
use App\Resource\Enum\FoundBy;
use App\Resource\Enum\Location;
use App\Resource\Manager\JobContributionsStatisticsManager;
use App\User\Enum\ExperienceYear;
use Carbon\Carbon;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class JobContributionStatisticsFixtures extends AbstractFixture implements DependentFixtureInterface
{
    /** @var Job[] */
    private array $jobs;
    private JobContributionsStatisticsManager $jobContributionsStatisticsManager;

    public function __construct(string $env, JobContributionsStatisticsManager $jobContributionsStatisticsManager)
    {
        parent::__construct($env);
        $this->jobContributionsStatisticsManager = $jobContributionsStatisticsManager;
    }

    public function load(ObjectManager $manager): void
    {
        if ('test' === $this->env) {
            $this->loadTestData($manager);
        } elseif ('dev' === $this->env) {
            $this->loadDevData();
        }
    }

    private function loadDevData(): void
    {
        $day = Carbon::now()->startOfDay();
        $this->jobContributionsStatisticsManager->createStatistics($day);
    }

    private function loadTestData(ObjectManager $manager): void
    {
        $startOfDay = Carbon::now()->startOfDay();
        foreach ($manager->getRepository(Job::class)->findAll() as $job) {
            $this->jobs[$job->getId()] = $job;
        }

        foreach ($this->getTestData() as $d) {
            $jobContributionStatistics = (new JobContributionStatistics())
                ->setDay($startOfDay)
                ->setJob($d['job'])
                ->setRemoteDaysPerWeekDistributionFree($d['remoteDaysPerWeekDistributionFree'])
                ->setRemoteDaysPerWeekDistributionWork($d['remoteDaysPerWeekDistributionWork'])
                ->setExperienceYearDistributionFree($d['experienceYearDistributionFree'])
                ->setExperienceYearDistributionWork($d['experienceYearDistributionWork'])
                ->setContractDurationDistributionFree($d['contractDurationDistributionFree'])
                ->setEmployerDistributionWork($d['employerDistributionWork'])
                ->setFoundByDistributionFree($d['foundByDistributionFree'])
                ->setOnCallPercentageFree($d['onCallPercentageFree'])
                ->setOnCallPercentageWork($d['onCallPercentageWork'])
                ->setAverageSearchJobDurationFree($d['averageSearchJobDurationFree'])
                ->setAverageSearchJobDurationWork($d['averageSearchJobDurationWork'])
                ->setAverageDailySalaryDirectly($d['averageDailySalaryDirectly'])
                ->setAverageDailySalaryWithIntermediary($d['averageDailySalaryWithIntermediary'])
                ->setAverageAnnualSalaryFinalClient($d['averageAnnualSalaryFinalClient'])
                ->setAverageAnnualSalaryNonFinalClient($d['averageAnnualSalaryNonFinalClient'])
                ->setSalaryExperienceDistributionFree($d['salaryExperienceDistributionFree'])
                ->setSalaryExperienceDistributionWork($d['salaryExperienceDistributionWork'])
                ->setSalaryExperienceLocationDistributionFree($d['salaryExperienceLocationDistributionFree'])
                ->setSalaryExperienceLocationDistributionWork($d['salaryExperienceLocationDistributionWork'])
            ;
            $manager->persist($jobContributionStatistics);
        }

        $manager->flush();
    }

    public function getTestData(): array
    {
        return [
            array_merge(
                $this->getDefaultData(),
                [
                    'job' => $this->jobs[1],
                ]
            ),
        ];
    }

    private function getDefaultData(): array
    {
        return [
            'jobId' => 1,
            'remoteDaysPerWeekDistributionFree' => [
                ['value' => 0, 'count' => 1500, 'percentage' => 0.15],
                ['value' => 1, 'count' => 400, 'percentage' => 0.04],
                ['value' => 2, 'count' => 2600, 'percentage' => 0.26],
                ['value' => 3, 'count' => 2500, 'percentage' => 0.25],
                ['value' => 4, 'count' => 1800, 'percentage' => 0.18],
                ['value' => 5, 'count' => 1200, 'percentage' => 0.12],
            ],
            'remoteDaysPerWeekDistributionWork' => [
                ['value' => 0, 'count' => 500, 'percentage' => 0.05],
                ['value' => 1, 'count' => 1000, 'percentage' => 0.1],
                ['value' => 2, 'count' => 1000, 'percentage' => 0.1],
                ['value' => 3, 'count' => 3500, 'percentage' => 0.35],
                ['value' => 4, 'count' => 3000, 'percentage' => 0.3],
                ['value' => 5, 'count' => 1000, 'percentage' => 0.2],
            ],
            'experienceYearDistributionFree' => [
                ['value' => ExperienceYear::LESS_THAN_1_YEAR, 'count' => 1000, 'percentage' => 0.1],
                ['value' => ExperienceYear::YEARS_1_2, 'count' => 2500, 'percentage' => 0.25],
                ['value' => ExperienceYear::YEARS_3_4, 'count' => 1700, 'percentage' => 0.17],
                ['value' => ExperienceYear::YEARS_5_10, 'count' => 2300, 'percentage' => 0.23],
                ['value' => ExperienceYear::YEARS_11_15, 'count' => 1500, 'percentage' => 0.15],
                ['value' => ExperienceYear::MORE_THAN_15_YEARS, 'count' => 1000, 'percentage' => 0.1],
            ],
            'experienceYearDistributionWork' => [
                ['value' => ExperienceYear::LESS_THAN_1_YEAR, 'count' => 1200, 'percentage' => 0.12],
                ['value' => ExperienceYear::YEARS_1_2, 'count' => 1900, 'percentage' => 0.19],
                ['value' => ExperienceYear::YEARS_3_4, 'count' => 2000, 'percentage' => 0.2],
                ['value' => ExperienceYear::YEARS_5_10, 'count' => 2200, 'percentage' => 0.22],
                ['value' => ExperienceYear::YEARS_11_15, 'count' => 1900, 'percentage' => 0.19],
                ['value' => ExperienceYear::MORE_THAN_15_YEARS, 'count' => 800, 'percentage' => 0.08],
            ],
            'employerDistributionWork' => [
                ['value' => Employer::FINAL_CLIENT, 'count' => 3000, 'percentage' => 0.3],
                ['value' => Employer::AGENCY, 'count' => 2500, 'percentage' => 0.25],
                ['value' => Employer::DIGITAL_SERVICE_COMPANY, 'count' => 1500, 'percentage' => 0.15],
                ['value' => Employer::RECRUITMENT_AGENCY, 'count' => 3000, 'percentage' => 0.3],
            ],
            'foundByDistributionFree' => [
                ['value' => FoundBy::FREEWORK, 'count' => 2000, 'percentage' => 0.2],
                ['value' => FoundBy::DIRECTLY, 'count' => 3500, 'percentage' => 0.35],
                ['value' => FoundBy::INTERMEDIARY, 'count' => 4500, 'percentage' => 0.45],
            ],
            'contractDurationDistributionFree' => [
                ['value' => 5, 'count' => 600, 'percentage' => 0.06],
                ['value' => 15, 'count' => 2900, 'percentage' => 0.29],
                ['value' => 30, 'count' => 4000, 'percentage' => 0.4],
                ['value' => 90, 'count' => 500, 'percentage' => 0.05],
                ['value' => 160, 'count' => 800, 'percentage' => 0.08],
                ['value' => 200, 'count' => 700, 'percentage' => 0.07],
                ['value' => 260, 'count' => 500, 'percentage' => 0.05],
            ],
            'onCallPercentageFree' => 65,
            'onCallPercentageWork' => 40,
            'averageSearchJobDurationFree' => 4,
            'averageSearchJobDurationWork' => 3,
            'averageDailySalaryDirectly' => 562,
            'averageDailySalaryWithIntermediary' => 865,
            'averageAnnualSalaryFinalClient' => 42891,
            'averageAnnualSalaryNonFinalClient' => 60051,
            'salaryExperienceDistributionFree' => [
                ExperienceYear::LESS_THAN_1_YEAR => [
                    'average' => 270,
                    'formattedAverage' => "270\u{a0}€",
                    'min' => 250,
                    'formattedMin' => "250\u{a0}€",
                    'max' => 300,
                    'formattedMax' => "300\u{a0}€",
                    'data' => [500, 0, 0, 0, 100, 100, 0, 0, 0, 500],
                    'labels' => [
                        '250-255 €',
                        '256-260 €',
                        '261-265 €',
                        '266-270 €',
                        '271-275 €',
                        '276-280 €',
                        '281-285 €',
                        '286-290 €',
                        '291-295 €',
                        '296-300 €',
                    ],
                ],
                ExperienceYear::YEARS_1_2 => [
                    'average' => 352,
                    'formattedAverage' => "352\u{a0}€",
                    'min' => 300,
                    'formattedMin' => "300\u{a0}€",
                    'max' => 375,
                    'formattedMax' => "375\u{a0}€",
                    'data' => [400, 0, 0, 0, 900, 0, 0, 0, 0, 600],
                    'labels' => [
                        '325-330 €',
                        '331-335 €',
                        '336-340 €',
                        '341-345 €',
                        '346-350 €',
                        '351-355 €',
                        '356-360 €',
                        '361-365 €',
                        '366-370 €',
                        '371-375 €',
                    ],
                ],
                ExperienceYear::YEARS_3_4 => [
                    'average' => 472,
                    'formattedAverage' => "472\u{a0}€",
                    'min' => 375,
                    'formattedMin' => "375\u{a0}€",
                    'max' => 500,
                    'formattedMax' => "500\u{a0}€",
                    'data' => [500, 0, 100, 0, 0, 500, 0, 0, 0, 900],
                    'labels' => [
                        '375-388 €',
                        '388-400 €',
                        '401-412 €',
                        '414-425 €',
                        '426-438 €',
                        '438-450 €',
                        '451-462 €',
                        '464-475 €',
                        '476-488 €',
                        '488-500 €',
                    ],
                ],
                ExperienceYear::YEARS_5_10 => [
                    'average' => 592,
                    'formattedAverage' => "592\u{a0}€",
                    'min' => 520,
                    'formattedMin' => "520\u{a0}€",
                    'max' => 620,
                    'formattedMax' => "620\u{a0}€",
                    'data' => [200, 300, 0, 0, 0, 0, 700, 0, 0, 1000],
                    'labels' => [
                        '500-512 €',
                        '513-524 €',
                        '525-536 €',
                        '537-548 €',
                        '549-560 €',
                        '561-572 €',
                        '573-584 €',
                        '585-596 €',
                        '597-608 €',
                        '609-620 €',
                    ],
                ],
                ExperienceYear::YEARS_11_15 => [
                    'average' => 687,
                    'formattedAverage' => "687\u{a0}€",
                    'min' => 620,
                    'formattedMin' => "620\u{a0}€",
                    'max' => 725,
                    'formattedMax' => "725\u{a0}€",
                    'data' => [400, 0, 300, 0, 0, 0, 0, 500, 0, 700],
                    'labels' => [
                        '620-630 €',
                        '632-641 €',
                        '642-652 €',
                        '652-662 €',
                        '663-672 €',
                        '674-683 €',
                        '684-694 €',
                        '694-704 €',
                        '705-714 €',
                        '716-725 €',
                    ],
                ],
                ExperienceYear::MORE_THAN_15_YEARS => [
                    'average' => 865,
                    'formattedAverage' => "865\u{a0}€",
                    'min' => 725,
                    'formattedMin' => "725\u{a0}€",
                    'max' => 1000,
                    'formattedMax' => "1\u{202f}000\u{a0}€",
                    'data' => [300, 0, 0, 0, 200, 0, 0, 0, 0, 300],
                    'labels' => [
                        '800-820 €',
                        '821-840 €',
                        '841-860 €',
                        '861-880 €',
                        '881-900 €',
                        '901-920 €',
                        '921-940 €',
                        '941-960 €',
                        '961-980 €',
                        '981-1k €',
                    ],
                ],
            ],
            'salaryExperienceDistributionWork' => [
                ExperienceYear::LESS_THAN_1_YEAR => [
                    'average' => 33450,
                    'formattedAverage' => "33\u{202f}450\u{a0}€",
                    'min' => 32000,
                    'formattedMin' => "32\u{202f}000\u{a0}€",
                    'max' => 37000,
                    'formattedMax' => "37\u{202f}000\u{a0}€",
                    'data' => [500, 0, 0, 250, 0, 150, 0, 0, 0, 100],
                    'labels' => [
                        "32k-32\u{202f}500 €",
                        "32\u{202f}501-33k €",
                        "33\u{202f}001-33\u{202f}500 €",
                        "33\u{202f}501-34k €",
                        "34\u{202f}001-34\u{202f}500 €",
                        "34\u{202f}501-35k €",
                        "35\u{202f}001-35\u{202f}500 €",
                        "35\u{202f}501-36k €",
                        "36\u{202f}001-36\u{202f}500 €",
                        "36\u{202f}501-37k €",
                    ],
                ],
                ExperienceYear::YEARS_1_2 => [
                    'average' => 40400,
                    'formattedAverage' => "40\u{202f}400\u{a0}€",
                    'min' => 37000,
                    'formattedMin' => "37\u{202f}000\u{a0}€",
                    'max' => 42000,
                    'formattedMax' => "42\u{202f}000\u{a0}€",
                    'data' => [200, 0, 0, 400, 0, 900, 0, 0, 0, 1000],
                    'labels' => [
                        "37k-37\u{202f}500 €",
                        "37\u{202f}501-38k €",
                        "38\u{202f}001-38\u{202f}500 €",
                        "38\u{202f}501-39k €",
                        "39\u{202f}001-39\u{202f}500 €",
                        "39\u{202f}501-40k €",
                        "40\u{202f}001-40\u{202f}500 €",
                        "40\u{202f}501-41k €",
                        "41\u{202f}001-41\u{202f}500 €",
                        "41\u{202f}501-42k €",
                    ],
                ],
                ExperienceYear::YEARS_3_4 => [
                    'average' => 47764,
                    'formattedAverage' => "47\u{202f}764\u{a0}€",
                    'min' => 42000,
                    'formattedMin' => "42\u{202f}000\u{a0}€",
                    'max' => 49000,
                    'formattedMax' => "49\u{202f}000\u{a0}€",
                    'data' => [100, 0, 0, 0, 100, 0, 0, 500, 0, 1000],
                    'labels' => [
                        "42k-42\u{202f}700 €",
                        "42\u{202f}701-43\u{202f}400 €",
                        "43\u{202f}401-44\u{202f}100 €",
                        "44\u{202f}101-44\u{202f}800 €",
                        "44\u{202f}801-45\u{202f}500 €",
                        "45\u{202f}501-46\u{202f}200 €",
                        "46\u{202f}201-46\u{202f}900 €",
                        "46\u{202f}901-47\u{202f}600 €",
                        "47\u{202f}601-48\u{202f}300 €",
                        "48\u{202f}301-49k €",
                    ],
                ],
                ExperienceYear::YEARS_5_10 => [
                    'average' => 53954,
                    'formattedAverage' => "60\u{202f}051\u{a0}€",
                    'min' => 52000,
                    'formattedMin' => "60\u{202f}051\u{a0}€",
                    'max' => 55000,
                    'formattedMax' => "60\u{202f}051\u{a0}€",
                    'data' => [300, 0, 0, 700, 0, 0, 0, 0, 0, 1200],
                    'labels' => [
                        "52k-52\u{202f}300 €",
                        "52\u{202f}301-52\u{202f}600 €",
                        "52\u{202f}601-52\u{202f}900 €",
                        "52\u{202f}901-53\u{202f}200 €",
                        "53\u{202f}201-53\u{202f}500 €",
                        "53\u{202f}501-53\u{202f}800 €",
                        "53\u{202f}801-54\u{202f}100 €",
                        "54\u{202f}101-54\u{202f}400 €",
                        "54\u{202f}401-54\u{202f}700 €",
                        "54\u{202f}701-55k €",
                    ],
                ],
                ExperienceYear::YEARS_11_15 => [
                    'average' => 60600,
                    'formattedAverage' => "60\u{202f}600\u{a0}€",
                    'min' => 55000,
                    'formattedMin' => "55\u{202f}000\u{a0}€",
                    'max' => 65000,
                    'formattedMax' => "65\u{202f}000\u{a0}€",
                    'data' => [200, 0, 300, 0, 500, 0, 0, 0, 0, 500],
                    'labels' => [
                        '55k-56k €',
                        "56\u{202f}001-57k €",
                        "57\u{202f}001-58k €",
                        "58\u{202f}001-59k €",
                        "59\u{202f}001-60k €",
                        "60\u{202f}001-61k €",
                        "61\u{202f}001-62k €",
                        "62\u{202f}001-63k €",
                        "63\u{202f}001-64k €",
                        "64\u{202f}001-65k €",
                    ],
                ],
                ExperienceYear::MORE_THAN_15_YEARS => [
                    'average' => 68900,
                    'formattedAverage' => "68\u{202f}900\u{a0}€",
                    'min' => 65000,
                    'formattedMin' => "60\u{202f}500\u{a0}€",
                    'max' => 75000,
                    'formattedMax' => "75\u{202f}000\u{a0}€",
                    'data' => [200, 0, 500, 0, 0, 0, 200, 0, 0, 100],
                    'labels' => [
                        '65k-66k €',
                        "66\u{202f}001-67k €",
                        "67\u{202f}001-68k €",
                        "68\u{202f}001-69k €",
                        "69\u{202f}001-70k €",
                        "70\u{202f}001-71k €",
                        "71\u{202f}001-72k €",
                        "72\u{202f}001-73k €",
                        "73\u{202f}001-74k €",
                        "74\u{202f}001-75k €",
                    ],
                ],
            ],
            'salaryExperienceLocationDistributionFree' => [
                'columns' => [Location::ILE_DE_FRANCE, Location::LARGE_CITIES, Location::SMALL_CITIES, Location::OUTSIDE_FRANCE],
                'lines' => [
                    [
                        'label' => ExperienceYear::LESS_THAN_1_YEAR,
                        'data' => ["250\u{a0}€", "277\u{a0}€", "300\u{a0}€", "300\u{a0}€"],
                    ],
                    [
                        'label' => ExperienceYear::YEARS_1_2,
                        'data' => ["300\u{a0}€", "342\u{a0}€", "375\u{a0}€", "375\u{a0}€"],
                    ],
                    [
                        'label' => ExperienceYear::YEARS_3_4,
                        'data' => ["430\u{a0}€", "587\u{a0}€", "500\u{a0}€", "500\u{a0}€"],
                    ],
                    [
                        'label' => ExperienceYear::YEARS_5_10,
                        'data' => ["620\u{a0}€", "620\u{a0}€", "620\u{a0}€", "620\u{a0}€"],
                    ],
                    [
                        'label' => ExperienceYear::YEARS_11_15,
                        'data' => ["620\u{a0}€", "670\u{a0}€", "712\u{a0}€", "725\u{a0}€"],
                    ],
                    [
                        'label' => ExperienceYear::MORE_THAN_15_YEARS,
                        'data' => ["725\u{a0}€", "840\u{a0}€", "1\u{a0}000\u{a0}€", "1\u{a0}000\u{a0}€"],
                    ],
                ],
            ],
            'salaryExperienceLocationDistributionWork' => [
                'columns' => [Location::ILE_DE_FRANCE, Location::LARGE_CITIES, Location::SMALL_CITIES, Location::OUTSIDE_FRANCE],
                'lines' => [
                    [
                        'label' => ExperienceYear::LESS_THAN_1_YEAR,
                        'data' => [
                            ['variableSalaryRate' => 0, 'salaryWithVariable' => "32\u{a0}000\u{a0}€"],
                            ['variableSalaryRate' => 0, 'salaryWithVariable' => "34\u{a0}000\u{a0}€"],
                            ['variableSalaryRate' => 0, 'salaryWithVariable' => "34\u{a0}750\u{a0}€"],
                            ['variableSalaryRate' => 0, 'salaryWithVariable' => "37\u{a0}000\u{a0}€"],
                        ],
                    ],
                    [
                        'label' => ExperienceYear::YEARS_1_2,
                        'data' => [
                            ['variableSalaryRate' => 0, 'salaryWithVariable' => "37\u{a0}000\u{a0}€"],
                            ['variableSalaryRate' => 0, 'salaryWithVariable' => "39\u{a0}884\u{a0}€"],
                            ['variableSalaryRate' => 0.01, 'salaryWithVariable' => "42\u{a0}500\u{a0}€"],
                            ['variableSalaryRate' => 0.01, 'salaryWithVariable' => "42\u{a0}500\u{a0}€"],
                        ],
                    ],
                    [
                        'label' => ExperienceYear::YEARS_3_4,
                        'data' => [
                            ['variableSalaryRate' => 0.01, 'salaryWithVariable' => "46\u{a0}333\u{a0}€"],
                            ['variableSalaryRate' => 0.01, 'salaryWithVariable' => "49\u{a0}000\u{a0}€"],
                            ['variableSalaryRate' => 0.01, 'salaryWithVariable' => "49\u{a0}500\u{a0}€"],
                            ['variableSalaryRate' => 0.01, 'salaryWithVariable' => "49\u{a0}500\u{a0}€"],
                        ],
                    ],
                    [
                        'label' => ExperienceYear::YEARS_5_10,
                        'data' => [
                            ['variableSalaryRate' => 0.04, 'salaryWithVariable' => "54\u{a0}363\u{a0}€"],
                            ['variableSalaryRate' => 0.1, 'salaryWithVariable' => "61\u{a0}000\u{a0}€"],
                            ['variableSalaryRate' => 0.11, 'salaryWithVariable' => "62\u{a0}142\u{a0}€"],
                            ['variableSalaryRate' => 0.15, 'salaryWithVariable' => "65\u{a0}000\u{a0}€"],
                        ],
                    ],
                    [
                        'label' => ExperienceYear::YEARS_11_15,
                        'data' => [
                            ['variableSalaryRate' => 0.15, 'salaryWithVariable' => "65\u{a0}000\u{a0}€"],
                            ['variableSalaryRate' => 0.15, 'salaryWithVariable' => "68\u{a0}800\u{a0}€"],
                            ['variableSalaryRate' => 0.14, 'salaryWithVariable' => "72\u{a0}692\u{a0}€"],
                            ['variableSalaryRate' => 0.13, 'salaryWithVariable' => "75\u{a0}000\u{a0}€"],
                        ],
                    ],
                    [
                        'label' => ExperienceYear::MORE_THAN_15_YEARS,
                        'data' => [
                            ['variableSalaryRate' => 0.13, 'salaryWithVariable' => "75\u{a0}000\u{a0}€"],
                            ['variableSalaryRate' => 0.13, 'salaryWithVariable' => "78\u{a0}000\u{a0}€"],
                            ['variableSalaryRate' => 0.12, 'salaryWithVariable' => "82\u{a0}000\u{a0}€"],
                            ['variableSalaryRate' => 0.12, 'salaryWithVariable' => "85\u{a0}000\u{a0}€"],
                        ],
                    ],
                ],
            ],
        ];
    }

    public function getDependencies(): array
    {
        return [
            JobFixtures::class,
            ContributionFixtures::class,
        ];
    }
}
