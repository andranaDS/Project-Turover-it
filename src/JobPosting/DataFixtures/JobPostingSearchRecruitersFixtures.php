<?php

namespace App\JobPosting\DataFixtures;

use App\Company\DataFixtures\CompanyBusinessActivitiesFixtures;
use App\Company\Entity\CompanyBusinessActivity;
use App\Core\DataFixtures\AbstractFixture;
use App\Core\DataFixtures\SkillsFixtures;
use App\Core\Entity\Job;
use App\Core\Entity\Location;
use App\Core\Entity\Skill;
use App\Core\Enum\Currency;
use App\Core\Util\Arrays;
use App\JobPosting\Entity\JobPostingSearchRecruiterAlert;
use App\JobPosting\Entity\JobPostingSearchRecruiterAlertLocation;
use App\JobPosting\Entity\JobPostingSearchRecruiterFavorite;
use App\JobPosting\Entity\JobPostingSearchRecruiterFavoriteLocation;
use App\JobPosting\Entity\JobPostingSearchRecruiterLog;
use App\JobPosting\Entity\JobPostingSearchRecruiterLogLocation;
use App\JobPosting\Enum\PublishedSince;
use App\JobPosting\Enum\RemoteMode;
use App\Recruiter\DataFixtures\RecruiterFixtures;
use App\Recruiter\Entity\Recruiter;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class JobPostingSearchRecruitersFixtures extends AbstractFixture implements DependentFixtureInterface
{
    private array $recruiters = [];
    private array $keywords = [];
    private array $activities = [];
    private array $locations = [];

    private DenormalizerInterface $denormalizer;

    public function __construct(string $env, DenormalizerInterface $denormalizer)
    {
        parent::__construct($env);
        $this->denormalizer = $denormalizer;
    }

    public function load(ObjectManager $manager): void
    {
        // fetch keywords
        $this->keywords = [];

        // skills
        foreach ($manager->getRepository(Skill::class)->findAll() as $skill) {
            $this->keywords[] = $skill->getName();
        }

        // jobs
        foreach ($manager->getRepository(Job::class)->findAll() as $job) {
            $this->keywords[] = $job->getName();
        }
        $this->keywords = array_filter($this->keywords);

        // fetch activities
        foreach ($manager->getRepository(CompanyBusinessActivity::class)->findAll() as $activity) {
            /* @var CompanyBusinessActivity $activity */
            $this->activities[$activity->getId()] = $activity;
        }

        // fetch recruiters
        foreach ($manager->getRepository(Recruiter::class)->findAll() as $recruiter) {
            /* @var Recruiter $recruiter */
            $this->recruiters[$recruiter->getEmail()] = $recruiter;
        }

        // fetch locations
        $this->locations = array_map(static function (string $location) {
            return json_decode($location, true);
        }, [
            'lyon' => '{"streetNumber":null,"streetName":null,"subLocality":null,"locality":"Lyon","localitySlug":"lyon","postalCode":null,"adminLevel1":"Auvergne-Rhône-Alpes","adminLevel1Slug":"auvergne-rhone-alpes","adminLevel2":"Métropole de Lyon","adminLevel2Slug":"metropole-de-lyon","country":"France","countryCode":"FR","latitude":"45.7578137","longitude":"4.8320114"}',
            'paris' => '{"streetNumber":null,"streetName":null,"subLocality":"Paris","locality":"Paris","localitySlug":"paris","postalCode":null,"adminLevel1":"Île-de-France","adminLevel1Slug":"ile-de-france","adminLevel2":null,"adminLevel2Slug":null,"country":"France","countryCode":"FR","latitude":"48.8588897","longitude":"2.3200410217201"}',
            'idf' => '{"streetNumber":null,"streetName":null,"subLocality":null,"locality":null,"localitySlug":null,"postalCode":null,"adminLevel1":"Île-de-France","adminLevel1Slug":"ile-de-france","adminLevel2":null,"adminLevel2Slug":null,"country":"France","countryCode":"FR","latitude":"48.6443057","longitude":"2.7537863"}',
        ]);

        if ('test' === $this->env) {
            foreach ($this->getTestData() as $entityClass => $datas) {
                foreach ($datas as $data) {
                    // @phpstan-ignore-next-line
                    $this->persistEntity(new $entityClass(), $data, $manager);
                }
            }
        } else {
            foreach ($this->getDevData() as $filters) {
                if (0 === mt_rand(0, 5)) {
                    $this->persistEntity(new JobPostingSearchRecruiterFavorite(), $filters, $manager);
                }
                if (0 === mt_rand(0, 5)) {
                    $this->persistEntity((new JobPostingSearchRecruiterAlert())->setActive((bool) mt_rand(0, 1)), $filters, $manager);
                }
                $this->persistEntity(new JobPostingSearchRecruiterLog(), $filters, $manager);
            }
        }

        $manager->flush();
    }

    /**
     * @param JobPostingSearchRecruiterAlert|JobPostingSearchRecruiterFavorite|JobPostingSearchRecruiterLog $entity
     */
    public function persistEntity($entity, array $filters, ObjectManager $manager): void
    {
        $entity
            ->setKeywords($filters['searchKeywords'])
            ->setRemoteMode($filters['remoteMode'])
            ->setPublishedSince($filters['publishedSince'])
            ->setMinDailySalary($filters['minDailySalary'])
            ->setMaxDailySalary($filters['maxDailySalary'])
            ->setCurrency($filters['currency'])
            ->setMinDuration($filters['minDuration'])
            ->setMaxDuration($filters['maxDuration'])
            ->setIntercontractOnly($filters['intercontractOnly'])
            ->setStartsAt($filters['startsAt'])
            ->setBusinessActivity($filters['companyBusinessActivity'])
            ->setRecruiter($filters['recruiter'])
            ->setCreatedAt($filters['createdAt'])
            ->setUpdatedAt($filters['updatedAt'])
            ->setIp($filters['ip'])
        ;

        if ($entity instanceof JobPostingSearchRecruiterAlert) {
            $entity->setTitle($filters['title']);
        }

        if (\is_array($filters['locations']) && \count($filters['locations']) > 0) {
            foreach ($filters['locations'] as $location) {
                $class = \get_class($entity) . 'Location';
                /** @var JobPostingSearchRecruiterAlertLocation|JobPostingSearchRecruiterFavoriteLocation|JobPostingSearchRecruiterLogLocation $entityLocation */
                $entityLocation = new $class();
                $entityLocation->setLocation($this->denormalizer->denormalize($location, Location::class));

                $entity->addLocation($entityLocation);
            }
        }

        $manager->persist($entity);
    }

    public function getDevData(): array
    {
        $data = [];
        $faker = Factory::create('fr_Fr');
        $createdAt = $faker->dateTimeBetween('- 6 months', '- 1 month');
        $filtersCount = 2000;

        for ($i = 0; $i < $filtersCount; ++$i) {
            $data[] = [
                'title' => $faker->sentence(random_int(1, 3)),
                'searchKeywords' => mt_rand(0, 1) ? implode(', ', Arrays::getRandomSubarray($this->keywords, 1, 3)) : null,
                'remoteMode' => mt_rand(0, 1) ? array_values(Arrays::getRandomSubarray(RemoteMode::getConstants())) : [],
                'publishedSince' => mt_rand(0, 1) ? Arrays::getRandom(PublishedSince::getConstants()) : null,
                'minDailySalary' => mt_rand(0, 1) ? mt_rand(150, 450) : null,
                'maxDailySalary' => mt_rand(0, 1) ? mt_rand(450, 1000) : null,
                'currency' => mt_rand(0, 1) ? Currency::EUR : null,
                'minDuration' => mt_rand(0, 1) ? mt_rand(5, 90) : null,
                'maxDuration' => mt_rand(0, 1) ? mt_rand(90, 300) : null,
                'intercontractOnly' => mt_rand(0, 1),
                'startsAt' => mt_rand(0, 1) ? $faker->dateTimeBetween('now', '+6 months') : null,
                'companyBusinessActivity' => mt_rand(0, 1) ? Arrays::getRandom($this->activities) : null,
                'recruiter' => Arrays::getRandom($this->recruiters),
                'createdAt' => $createdAt,
                'updatedAt' => $faker->dateTimeBetween($createdAt),
                'locations' => mt_rand(0, 1) ? array_values(Arrays::getRandomSubarray($this->locations, 1, 3)) : [],
                'ip' => $faker->ipv4(),
            ];
        }

        return $data;
    }

    public function getTestData(): array
    {
        return [
            JobPostingSearchRecruiterAlert::class => [
                [
                    'title' => 'Job Posting Recruiter 1 Alert 1 title',
                    'recruiter' => $this->recruiters['walter.white@breaking-bad.com'],
                    'searchKeywords' => 'Job Posting Recruiter 1 Alert 1',
                    'remoteMode' => [RemoteMode::FULL],
                    'publishedSince' => PublishedSince::FROM_1_TO_7_DAYS,
                    'minDailySalary' => 150,
                    'maxDailySalary' => 450,
                    'currency' => Currency::EUR,
                    'minDuration' => 5,
                    'maxDuration' => 90,
                    'intercontractOnly' => false,
                    'startsAt' => (new \DateTime())->modify('+30 days')->setTime(8, 00),
                    'companyBusinessActivity' => $this->activities[1],
                    'createdAt' => new \DateTime('2022-01-01 12:00:00'),
                    'updatedAt' => new \DateTime('2022-01-01 12:30:00'),
                    'active' => true,
                    'locations' => null,
                    'ip' => '1.1.1.1',
                ],
                [
                    'title' => 'Job Posting Recruiter 1 Alert 2 title',
                    'recruiter' => $this->recruiters['walter.white@breaking-bad.com'],
                    'searchKeywords' => 'Job Posting Recruiter 1 Alert 2',
                    'remoteMode' => [RemoteMode::PARTIAL],
                    'publishedSince' => PublishedSince::FROM_1_TO_7_DAYS,
                    'minDailySalary' => 350,
                    'maxDailySalary' => 650,
                    'currency' => Currency::EUR,
                    'minDuration' => null,
                    'maxDuration' => 360,
                    'intercontractOnly' => true,
                    'startsAt' => (new \DateTime())->modify('+30 days')->setTime(8, 30),
                    'companyBusinessActivity' => $this->activities[2],
                    'createdAt' => new \DateTime('2022-01-01 13:00:00'),
                    'updatedAt' => new \DateTime('2022-01-01 13:30:00'),
                    'active' => true,
                    'locations' => [$this->locations['paris']],
                    'ip' => '1.1.1.2',
                ],
                [
                    'title' => 'Job Posting Recruiter 2 Alert 1 title',
                    'recruiter' => $this->recruiters['jesse.pinkman@breaking-bad.com'],
                    'searchKeywords' => 'Job Posting Recruiter 2 Alert 1',
                    'remoteMode' => [RemoteMode::NONE],
                    'publishedSince' => PublishedSince::LESS_THAN_24_HOURS,
                    'minDailySalary' => 350,
                    'maxDailySalary' => 650,
                    'currency' => Currency::EUR,
                    'minDuration' => null,
                    'maxDuration' => 360,
                    'intercontractOnly' => true,
                    'startsAt' => (new \DateTime())->modify('+10 days')->setTime(8, 30),
                    'companyBusinessActivity' => $this->activities[1],
                    'createdAt' => new \DateTime('2022-01-01 14:00:00'),
                    'updatedAt' => new \DateTime('2022-01-01 14:30:00'),
                    'active' => true,
                    'locations' => null,
                    'ip' => '1.1.1.3',
                ],
                [
                    'title' => 'Job Posting Recruiter 2 Alert 2 title',
                    'recruiter' => $this->recruiters['jesse.pinkman@breaking-bad.com'],
                    'searchKeywords' => 'Job Posting Recruiter 2 Alert 2 Non Active',
                    'remoteMode' => [RemoteMode::NONE],
                    'publishedSince' => PublishedSince::FROM_15_DAYS_TO_1_MONTH,
                    'minDailySalary' => 350,
                    'maxDailySalary' => 650,
                    'currency' => Currency::EUR,
                    'minDuration' => null,
                    'maxDuration' => null,
                    'intercontractOnly' => true,
                    'startsAt' => (new \DateTime())->modify('+10 days')->setTime(8, 30),
                    'companyBusinessActivity' => $this->activities[1],
                    'createdAt' => new \DateTime('2022-01-01 14:00:00'),
                    'updatedAt' => new \DateTime('2022-01-01 14:30:00'),
                    'active' => false,
                    'locations' => [$this->locations['lyon']],
                    'ip' => '1.1.1.4',
                ],
            ],
            JobPostingSearchRecruiterFavorite::class => [
                [
                    'recruiter' => $this->recruiters['walter.white@breaking-bad.com'],
                    'searchKeywords' => 'Job Posting Recruiter 1 Favorite 1',
                    'remoteMode' => [RemoteMode::FULL],
                    'publishedSince' => null,
                    'minDailySalary' => 150,
                    'maxDailySalary' => 450,
                    'currency' => Currency::EUR,
                    'minDuration' => null,
                    'maxDuration' => null,
                    'intercontractOnly' => true,
                    'startsAt' => null,
                    'companyBusinessActivity' => $this->activities[1],
                    'createdAt' => new \DateTime('2022-01-02 12:00:00'),
                    'updatedAt' => new \DateTime('2022-01-02 12:30:00'),
                    'locations' => null,
                    'ip' => '1.1.1.1',
                ],
                [
                    'recruiter' => $this->recruiters['walter.white@breaking-bad.com'],
                    'searchKeywords' => 'Job Posting Recruiter 1 Favorite 2',
                    'remoteMode' => [RemoteMode::FULL],
                    'publishedSince' => PublishedSince::FROM_1_TO_7_DAYS,
                    'minDailySalary' => 150,
                    'maxDailySalary' => 450,
                    'currency' => Currency::EUR,
                    'minDuration' => 5,
                    'maxDuration' => 90,
                    'intercontractOnly' => false,
                    'startsAt' => (new \DateTime())->modify('+30 days')->setTime(8, 00),
                    'companyBusinessActivity' => $this->activities[1],
                    'createdAt' => new \DateTime('2022-01-02 13:00:00'),
                    'updatedAt' => new \DateTime('2022-01-02 13:30:00'),
                    'locations' => [$this->locations['paris'], $this->locations['lyon']],
                    'ip' => '1.1.1.2',
                ],
                [
                    'recruiter' => $this->recruiters['jesse.pinkman@breaking-bad.com'],
                    'searchKeywords' => 'Job Posting Recruiter 2 Favorite 1',
                    'remoteMode' => [RemoteMode::FULL],
                    'publishedSince' => null,
                    'minDailySalary' => 350,
                    'maxDailySalary' => 650,
                    'currency' => Currency::EUR,
                    'minDuration' => null,
                    'maxDuration' => 360,
                    'intercontractOnly' => true,
                    'startsAt' => (new \DateTime())->modify('+10 days')->setTime(8, 30),
                    'companyBusinessActivity' => $this->activities[1],
                    'createdAt' => new \DateTime('2022-01-02 14:00:00'),
                    'updatedAt' => new \DateTime('2022-01-02 14:30:00'),
                    'locations' => [$this->locations['paris'], $this->locations['idf']],
                    'ip' => '1.1.1.3',
                ],
            ],
            JobPostingSearchRecruiterLog::class => [
                [
                    'recruiter' => $this->recruiters['walter.white@breaking-bad.com'],
                    'searchKeywords' => 'Job Posting Recruiter 1 Search 1',
                    'remoteMode' => [RemoteMode::FULL],
                    'publishedSince' => PublishedSince::FROM_1_TO_7_DAYS,
                    'minDailySalary' => 150,
                    'maxDailySalary' => 450,
                    'currency' => Currency::EUR,
                    'minDuration' => 5,
                    'maxDuration' => 90,
                    'intercontractOnly' => false,
                    'startsAt' => (new \DateTime())->modify('+30 days')->setTime(8, 00),
                    'companyBusinessActivity' => $this->activities[1],
                    'createdAt' => new \DateTime('2022-01-03 12:00:00'),
                    'updatedAt' => new \DateTime('2022-01-03 12:30:00'),
                    'locations' => null,
                    'ip' => '1.1.1.1',
                ],
                [
                    'recruiter' => $this->recruiters['walter.white@breaking-bad.com'],
                    'searchKeywords' => 'Job Posting Recruiter 1 Search 2',
                    'remoteMode' => [RemoteMode::FULL],
                    'publishedSince' => PublishedSince::FROM_1_TO_7_DAYS,
                    'minDailySalary' => 150,
                    'maxDailySalary' => 450,
                    'currency' => Currency::EUR,
                    'minDuration' => 5,
                    'maxDuration' => 90,
                    'intercontractOnly' => false,
                    'startsAt' => (new \DateTime())->modify('+30 days')->setTime(8, 00),
                    'companyBusinessActivity' => $this->activities[1],
                    'createdAt' => new \DateTime('2022-01-03 13:00:00'),
                    'updatedAt' => new \DateTime('2022-01-03 13:30:00'),
                    'locations' => [$this->locations['paris'], $this->locations['lyon']],
                    'ip' => '1.1.1.2',
                ],
                [
                    'recruiter' => $this->recruiters['walter.white@breaking-bad.com'],
                    'searchKeywords' => 'Job Posting Recruiter 1 Search 3',
                    'remoteMode' => [RemoteMode::FULL],
                    'publishedSince' => PublishedSince::FROM_1_TO_7_DAYS,
                    'minDailySalary' => 150,
                    'maxDailySalary' => 450,
                    'currency' => Currency::EUR,
                    'minDuration' => 5,
                    'maxDuration' => 90,
                    'intercontractOnly' => false,
                    'startsAt' => (new \DateTime())->modify('+30 days')->setTime(8, 00),
                    'companyBusinessActivity' => $this->activities[2],
                    'createdAt' => new \DateTime('2022-01-03 14:00:00'),
                    'updatedAt' => new \DateTime('2022-01-03 14:30:00'),
                    'locations' => null,
                    'ip' => '1.1.1.3',
                ],
                [
                    'recruiter' => $this->recruiters['jesse.pinkman@breaking-bad.com'],
                    'searchKeywords' => 'Job Posting Recruiter 2 Search 1',
                    'remoteMode' => [RemoteMode::FULL],
                    'publishedSince' => PublishedSince::FROM_1_TO_7_DAYS,
                    'minDailySalary' => 150,
                    'maxDailySalary' => 450,
                    'currency' => Currency::EUR,
                    'minDuration' => 5,
                    'maxDuration' => 90,
                    'intercontractOnly' => false,
                    'startsAt' => (new \DateTime())->modify('+30 days')->setTime(8, 00),
                    'companyBusinessActivity' => $this->activities[2],
                    'createdAt' => new \DateTime('2022-01-04 14:00:00'),
                    'updatedAt' => new \DateTime('2022-01-04 14:30:00'),
                    'locations' => null,
                    'ip' => '1.1.1.4',
                ],
            ],
        ];
    }

    public function getDependencies(): array
    {
        return [
            RecruiterFixtures::class,
            SkillsFixtures::class,
            CompanyBusinessActivitiesFixtures::class,
        ];
    }
}
