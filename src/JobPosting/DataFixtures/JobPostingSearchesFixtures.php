<?php

namespace App\JobPosting\DataFixtures;

use App\Core\DataFixtures\AbstractFixture;
use App\Core\Entity\Location;
use App\Core\Enum\Currency;
use App\JobPosting\Entity\JobPostingSearch;
use App\JobPosting\Entity\JobPostingSearchLocation;
use App\JobPosting\Enum\Contract;
use App\JobPosting\Enum\PublishedSince;
use App\JobPosting\Enum\RemoteMode;
use App\User\DataFixtures\UsersFixtures;
use App\User\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class JobPostingSearchesFixtures extends AbstractFixture implements DependentFixtureInterface
{
    private array $users;
    private array $locations;

    private DenormalizerInterface $denormalizer;

    public function __construct(string $env, DenormalizerInterface $denormalizer)
    {
        parent::__construct($env);
        $this->denormalizer = $denormalizer;
    }

    public function load(ObjectManager $manager): void
    {
        // fetch all users
        foreach ($manager->getRepository(User::class)->findAll() as $user) {
            /* @var User $user */
            $this->users[$user->getEmail()] = $user;
        }

        // fetch locations
        $this->locations = array_map(static function (string $location) {
            return json_decode($location, true);
        }, [
            'lyon' => '{"subLocality":null,"locality":"Lyon","localitySlug":"lyon","postalCode":null,"adminLevel1":"Auvergne-Rhône-Alpes","adminLevel1Slug":"auvergne-rhone-alpes","adminLevel2":"Métropole de Lyon","adminLevel2Slug":"metropole-de-lyon","country":"France","countryCode":"FR","latitude":"45.7578137","longitude":"4.8320114"}',
            'paris' => '{"subLocality":"Paris","locality":"Paris","localitySlug":"paris","postalCode":null,"adminLevel1":"Île-de-France","adminLevel1Slug":"ile-de-france","adminLevel2":null,"adminLevel2Slug":null,"country":"France","countryCode":"FR","latitude":"48.8588897","longitude":"2.3200410217201"}',
            'idf' => '{"subLocality":null,"locality":null,"localitySlug":null,"postalCode":null,"adminLevel1":"Île-de-France","adminLevel1Slug":"ile-de-france","adminLevel2":null,"adminLevel2Slug":null,"country":"France","countryCode":"FR","latitude":"48.6443057","longitude":"2.7537863"}',
        ]);

        // process data
        foreach ($this->getData() as $d) {
            $jobPostingSearch = (new JobPostingSearch())
                ->setTitle($d['title'])
                ->setUser($d['user'])
                ->setSearchKeywords($d['keywords'])
                ->setContracts($d['contracts'])
                ->setMinAnnualSalary($d['minAnnualSalary'])
                ->setMinDailySalary($d['minDailySalary'])
                ->setMinduration($d['minDuration'])
                ->setMaxduration($d['maxDuration'])
                ->setCurrency($d['currency'])
                ->setRemoteMode($d['remoteMode'])
                ->setActiveAlert($d['activeAlert'] ?? true)
                ->setPublishedSince($d['publishedSince'])
                ->setCreatedAt($d['createdAt'])
            ;

            $manager->persist($jobPostingSearch);
            $manager->flush();

            if (\is_array($d['locations'])) {
                foreach ($d['locations'] as $location) {
                    $jobPostingLocation = new JobPostingSearchLocation();
                    $jobPostingLocation->setLocation($this->denormalizer->denormalize($location, Location::class));
                    $jobPostingLocation->setJobPostingSearch($jobPostingSearch);

                    $jobPostingSearch->addLocation(
                        $jobPostingLocation
                    );
                }
                $manager->persist($jobPostingSearch);
            }
        }

        $manager->flush();
    }

    public function getDevData(): array
    {
        $faker = Faker::create('fr_FR');

        return [
            [
                'title' => $faker->sentence(random_int(3, 8)),
                'keywords' => 'php,java',
                'locations' => [$this->locations['paris']],
                'contracts' => [Contract::PERMANENT, Contract::CONTRACTOR],
                'minAnnualSalary' => null,
                'minDailySalary' => null,
                'currency' => null,
                'minDuration' => null,
                'maxDuration' => null,
                'remoteMode' => null,
                'publishedSince' => null,
                'user' => $this->users['thenry@free-work.fr'],
                'createdAt' => new \DateTime('2021-03-01 23:30:00'),
            ],
            [
                'title' => $faker->sentence(random_int(3, 8)),
                'keywords' => 'laravel',
                'locations' => [$this->locations['paris']],
                'contracts' => [Contract::PERMANENT, Contract::CONTRACTOR],
                'minAnnualSalary' => null,
                'minDailySalary' => null,
                'currency' => null,
                'minDuration' => null,
                'maxDuration' => null,
                'remoteMode' => null,
                'publishedSince' => null,
                'user' => $this->users['thenry@free-work.fr'],
                'createdAt' => new \DateTime('2021-03-01 23:30:00'),
                'activeAlert' => false,
            ],
            [
                'title' => $faker->sentence(random_int(3, 8)),
                'keywords' => 'symfony',
                'locations' => [$this->locations['paris']],
                'contracts' => [Contract::PERMANENT, Contract::CONTRACTOR],
                'minAnnualSalary' => null,
                'minDailySalary' => null,
                'currency' => null,
                'minDuration' => null,
                'maxDuration' => null,
                'remoteMode' => null,
                'publishedSince' => null,
                'user' => $this->users['thenry@free-work.fr'],
                'createdAt' => new \DateTime('2021-03-01 23:30:00'),
                'activeAlert' => true,
            ],
            [
                'title' => $faker->sentence(random_int(3, 8)),
                'keywords' => null,
                'locations' => null,
                'contracts' => [Contract::FIXED_TERM, Contract::CONTRACTOR],
                'minAnnualSalary' => null,
                'minDailySalary' => null,
                'currency' => null,
                'minDuration' => null,
                'maxDuration' => null,
                'remoteMode' => [RemoteMode::FULL],
                'publishedSince' => PublishedSince::LESS_THAN_24_HOURS,
                'user' => $this->users['zzidane@free-work.fr'],
                'createdAt' => new \DateTime('2021-03-01 23:45:00'),
            ],
            [
                'title' => $faker->sentence(random_int(3, 8)),
                'keywords' => null,
                'locations' => [$this->locations['paris'], $this->locations['lyon']],
                'contracts' => [Contract::CONTRACTOR],
                'minAnnualSalary' => null,
                'minDailySalary' => 600,
                'currency' => Currency::EUR,
                'minDuration' => 90,
                'maxDuration' => null,
                'remoteMode' => [RemoteMode::FULL, RemoteMode::PARTIAL],
                'publishedSince' => null,
                'user' => $this->users['jacques.delamballerie@free-work.fr'],
                'createdAt' => new \DateTime('2021-03-01 23:50:00'),
            ],
            [
                'title' => $faker->sentence(random_int(3, 8)),
                'keywords' => null,
                'locations' => [$this->locations['idf']],
                'contracts' => [Contract::CONTRACTOR],
                'minAnnualSalary' => null,
                'minDailySalary' => 200,
                'currency' => Currency::EUR,
                'minDuration' => 5,
                'maxDuration' => 90,
                'remoteMode' => [RemoteMode::PARTIAL],
                'publishedSince' => PublishedSince::FROM_1_TO_7_DAYS,
                'user' => $this->users['charlene.herent@free-work.fr'],
                'createdAt' => new \DateTime('2021-03-01 23:55:00'),
            ],
        ];
    }

    public function getTestData(): array
    {
        return [
            [
                'title' => 'JobPostingSearch 1 - User 6',
                'keywords' => 'php,java',
                'locations' => [$this->locations['paris']],
                'contracts' => [Contract::PERMANENT, Contract::FIXED_TERM],
                'minAnnualSalary' => null,
                'minDailySalary' => null,
                'currency' => null,
                'minDuration' => null,
                'maxDuration' => null,
                'remoteMode' => null,
                'publishedSince' => PublishedSince::LESS_THAN_24_HOURS,
                'user' => $this->users['claude.monet@free-work.fr'],
                'createdAt' => new \DateTime('2021-03-01 23:30:00'),
            ],
            [
                'title' => 'JobPostingSearch 2 - User 6',
                'keywords' => null,
                'locations' => null,
                'minAnnualSalary' => null,
                'minDailySalary' => null,
                'currency' => null,
                'contracts' => [Contract::CONTRACTOR],
                'minDuration' => null,
                'maxDuration' => null,
                'remoteMode' => [RemoteMode::FULL],
                'publishedSince' => PublishedSince::FROM_1_TO_7_DAYS,
                'user' => $this->users['claude.monet@free-work.fr'],
                'createdAt' => new \DateTime('2021-03-01 23:45:00'),
            ],
            [
                'title' => 'JobPostingSearch 1 - User 2',
                'keywords' => null,
                'locations' => [$this->locations['paris'], $this->locations['lyon']],
                'minAnnualSalary' => null,
                'minDailySalary' => 600,
                'currency' => Currency::EUR,
                'contracts' => [Contract::INTERNSHIP, Contract::FIXED_TERM],
                'minDuration' => 90,
                'maxDuration' => null,
                'remoteMode' => [RemoteMode::FULL, RemoteMode::PARTIAL],
                'publishedSince' => null,
                'user' => $this->users['admin@free-work.fr'],
                'createdAt' => new \DateTime('2021-03-01 23:50:00'),
            ],
            [
                'title' => 'JobPostingSearch 1 - User 7',
                'keywords' => null,
                'locations' => [$this->locations['idf']],
                'minAnnualSalary' => null,
                'minDailySalary' => 200,
                'currency' => Currency::EUR,
                'contracts' => [Contract::CONTRACTOR, Contract::PERMANENT],
                'minDuration' => 5,
                'maxDuration' => 90,
                'remoteMode' => [RemoteMode::PARTIAL],
                'publishedSince' => PublishedSince::FROM_15_DAYS_TO_1_MONTH,
                'user' => $this->users['vincent.van-gogh@free-work.fr'],
                'createdAt' => new \DateTime('2021-03-01 23:55:00'),
            ],
            [
                'title' => 'JobPostingSearch 3 - User 6 - Inactive',
                'keywords' => 'php,java',
                'locations' => [$this->locations['paris']],
                'contracts' => [Contract::PERMANENT, Contract::FIXED_TERM],
                'minAnnualSalary' => null,
                'minDailySalary' => null,
                'currency' => null,
                'minDuration' => null,
                'maxDuration' => null,
                'remoteMode' => null,
                'publishedSince' => PublishedSince::LESS_THAN_24_HOURS,
                'user' => $this->users['claude.monet@free-work.fr'],
                'createdAt' => new \DateTime('2021-03-01 23:30:00'),
                'activeAlert' => false,
            ],
        ];
    }

    public function getDependencies()
    {
        return [
            UsersFixtures::class,
        ];
    }
}
