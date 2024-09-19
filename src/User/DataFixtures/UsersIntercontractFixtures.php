<?php

namespace App\User\DataFixtures;

use App\Core\DataFixtures\AbstractFixture;
use App\Core\DataFixtures\SoftSkillsFixtures;
use App\Core\Entity\Job;
use App\Core\Entity\Location;
use App\Core\Entity\Skill;
use App\Core\Entity\SoftSkill;
use App\Core\Util\Arrays;
use App\Recruiter\Entity\Recruiter;
use App\User\Entity\User;
use App\User\Entity\UserDocument;
use App\User\Entity\UserFormation;
use App\User\Entity\UserJob;
use App\User\Entity\UserLanguage;
use App\User\Entity\UserMobility;
use App\User\Entity\UserSkill;
use App\User\Enum\Availability;
use App\User\Enum\ExperienceYear;
use App\User\Enum\Language;
use App\User\Enum\LanguageLevel;
use App\User\Manager\UserManager;
use Carbon\Carbon;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;
use Faker\Generator;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class UsersIntercontractFixtures extends AbstractFixture implements DependentFixtureInterface
{
    private array $softSkills = [];
    private array $skills = [];
    private array $jobs = [];
    private array $locations = [];
    private array $recruiters = [];
    private DenormalizerInterface $denormalizer;
    private Generator $faker;

    public function __construct(string $env, DenormalizerInterface $denormalizer)
    {
        parent::__construct($env);
        $this->denormalizer = $denormalizer;
        $this->faker = Faker::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        // fetch recruiters
        foreach ($manager->getRepository(Recruiter::class)->findAll() as $recruiter) {
            /* @var Recruiter $recruiter */
            $this->recruiters[$recruiter->getEmail()] = $recruiter;
        }

        // fetch softSkills
        foreach ($manager->getRepository(SoftSkill::class)->findAll() as $softSkill) {
            /* @var SoftSkill $softSkill */
            $this->softSkills[$softSkill->getId()] = $softSkill;
        }

        // fetch skills
        foreach ($manager->getRepository(Skill::class)->findBy([]) as $skill) {
            /* @var Skill $kill */
            $this->skills[$skill->getId()] = $skill;
        }

        // fetch jobs
        foreach ($manager->getRepository(Job::class)->findBy([], null, 3) as $job) {
            /* @var Job $job */
            $this->jobs[$job->getId()] = $job;
        }

        // fetch locations
        $this->locations = array_map(static function (string $location) {
            return json_decode($location, true);
        }, [
            'lyon' => '{"subLocality":null,"locality":"Lyon","localitySlug":"lyon","postalCode":null,"adminLevel1":"Auvergne-Rhône-Alpes","adminLevel1Slug":"auvergne-rhone-alpes","adminLevel2":"Métropole de Lyon","adminLevel2Slug":"metropole-de-lyon","country":"France","countryCode":"FR","latitude":"45.7578137","longitude":"4.8320114"}',
            'paris' => '{"subLocality":"Paris","locality":"Paris","localitySlug":"paris","postalCode":null,"adminLevel1":"Île-de-France","adminLevel1Slug":"ile-de-france","adminLevel2":null,"adminLevel2Slug":null,"country":"France","countryCode":"FR","latitude":"48.8588897","longitude":"2.3200410217201"}',
            'idf' => '{"subLocality":null,"locality":null,"localitySlug":null,"postalCode":null,"adminLevel1":"Île-de-France","adminLevel1Slug":"ile-de-france","adminLevel2":null,"adminLevel2Slug":null,"country":"France","countryCode":"FR","latitude":"48.6443057","longitude":"2.7537863"}',
        ]);

        foreach ($this->getData() as $dataUser) {
            $user = (new User())
                ->setFirstName($dataUser['firstName'] ?? null)
                ->setLastName($dataUser['lastName'] ?? null)
                ->setProfileJobTitle($dataUser['profileJobTitle'] ?? null)
                ->setExperienceYear($dataUser['experienceYear'] ?? null)
                ->setVisible($dataUser['visible'] ?? true)
                ->setAvailability($dataUser['availability'] ?? null)
                ->setIntroduceYourself($dataUser['introduceYourself'] ?? null)
                ->setAverageDailyRate($dataUser['averageDailyRate'] ?? null)
                ->setGrossAnnualSalary($dataUser['grossAnnualSalary'] ?? null)
                ->setReference($dataUser['reference'] ?? null)
                ->setContact($dataUser['contact'] ?? null)
                ->setNextAvailabilityAt(
                    $dataUser['nextAvailabilityAt'] ??
                    UserManager::calculateNextAvailabilityAt($dataUser['availability'] ?? null, $dataUser['statusUpdatedAt'] ?? null)
                )
                ->setCreatedBy($dataUser['createdBy'])
                ->setLocation($this->denormalizer->denormalize($dataUser['location'], Location::class))
            ;

            foreach ($dataUser['softSkills'] as $softSkill) {
                $user->addSoftSkill($softSkill);
            }

            if (null !== ($dataUser['formation'] ?? null)) {
                $formation = (new UserFormation())
                    ->setDiplomaLevel($dataUser['formation']['diplomaLevel'])
                ;
                $manager->persist($formation);
                $user->setFormation($formation);
            }

            if (null !== ($dataUser['documents'] ?? null)) {
                $document = (new UserDocument())
                    ->setContent($dataUser['documents'][0]['content'])
                ;
                $user->addDocument($document);
            }

            $manager->persist($user);

            foreach ($dataUser['skills'] as $skill) {
                $userSkill = (new UserSkill())
                ->setUser($user)
                ->setSkill($skill)
                ;
                $manager->persist($userSkill);
            }

            foreach ($this->jobs as $job) {
                $userJob = (new UserJob())
                    ->setUser($user)
                    ->setJob($job)
                ;
                $manager->persist($userJob);
            }

            foreach ($dataUser['languages'] as $language) {
                $userLanguage = (new UserLanguage())
                    ->setUser($user)
                    ->setLanguage($language['language'])
                    ->setLanguageLevel($language['languageLevel'])
                ;
                $manager->persist($userLanguage);
            }

            foreach ($dataUser['locations'] as $location) {
                $userMobility = (new UserMobility())
                    ->setUser($user)
                    ->setLocation($this->denormalizer->denormalize($location, Location::class))
                ;
                $manager->persist($userMobility);
            }
        }

        $manager->flush();
    }

    public function getDevData(): array
    {
        $now = Carbon::now();

        $usersCount = 25;
        for ($i = 0; $i < $usersCount; ++$i) {
            $itemCount = random_int(1, 3);
            $languages = [];
            for ($j = 0; $j < $itemCount; ++$j) {
                $languages[] = [
                    'language' => Arrays::getRandom(Language::getConstants()),
                    'languageLevel' => Arrays::getRandom(LanguageLevel::getConstants()),
                ];
            }

            $data[] = [
                'firstName' => $this->faker->firstName(),
                'lastName' => $this->faker->lastName(),
                'profileJobTitle' => $this->faker->jobTitle,
                'experienceYear' => Arrays::getRandom(ExperienceYear::getConstants()),
                'availability' => Arrays::getRandom(Availability::getConstants()),
                'visible' => mt_rand(0, 1),
                'averageDailyRate' => $this->faker->numberBetween(300, 1500),
                'grossAnnualSalary' => $this->faker->numberBetween(20000, 70000),
                'introduceYourself' => $this->faker->text(mt_rand(300, 600)),
                'statusUpdatedAt' => $now->copy()->subDays(15),
                'skills' => Arrays::getRandomSubarray($this->skills, 1, 5),
                'softSkills' => Arrays::getRandomSubarray($this->softSkills, 0, 3),
                'formation' => [
                    'diplomaLevel' => mt_rand(0, 10),
                ],
                'reference' => mt_rand(0, 1) ? $this->faker->randomNumber(8) : null,
                'contact' => $this->faker->text(mt_rand(100, 200)),
                'nextAvailabilityAt' => $now->copy()->addDays(14),
                'languages' => $languages,
                'location' => Arrays::getRandom($this->locations),
                'locations' => Arrays::getRandomSubarray($this->locations),
                'createdBy' => Arrays::getRandom($this->recruiters),
            ];
        }

        return $data;
    }

    public function getTestData(): array
    {
        $now = Carbon::now();

        return [
            [
                'firstName' => 'FirstName Intercontract 1 Company 1',
                'lastName' => 'LastName Intercontract 1 Company 1',
                'profileJobTitle' => 'Profile Intercontract 1 Company 1',
                'experienceYear' => ExperienceYear::YEARS_1_2,
                'availability' => Availability::WITHIN_1_MONTH,
                'visible' => true,
                'averageDailyRate' => 500,
                'grossAnnualSalary' => 50000,
                'introduceYourself' => 'Introduce Intercontract 1 Company 1',
                'statusUpdatedAt' => $now->copy()->subDays(15),
                'skills' => [
                    $this->skills[1],
                    $this->skills[2],
                ],
                'softSkills' => [
                    $this->softSkills[1],
                    $this->softSkills[3],
                ],
                'formation' => [
                    'diplomaLevel' => 5,
                ],
                'reference' => 'C10001',
                'contact' => 'Contact Intercontract 1 Company 1',
                'nextAvailabilityAt' => $now->copy()->addDays(14),
                'languages' => [
                    [
                        'language' => Language::LANGUAGE_FR,
                        'languageLevel' => LanguageLevel::NATIVE_OR_BILINGUAL,
                    ],
                    [
                        'language' => Language::LANGUAGE_EN,
                        'languageLevel' => LanguageLevel::LIMITED_PROFESSIONAL_SKILLS,
                    ],
                ],
                'location' => $this->locations['paris'],
                'locations' => [
                    $this->locations['paris'],
                    $this->locations['idf'],
                ],
                'documents' => [
                    [
                        'content' => 'Contenu CV Profile Intercontract 1 Company 1',
                    ],
                ],
                'createdBy' => $this->recruiters['walter.white@breaking-bad.com'],
            ],
            [
                'firstName' => 'FirstName Intercontract 2 Company 1',
                'lastName' => 'LastName Intercontract 2 Company 1',
                'profileJobTitle' => 'Profile Intercontract 2 Company 1',
                'experienceYear' => ExperienceYear::YEARS_3_4,
                'availability' => Availability::IMMEDIATE,
                'visible' => false,
                'averageDailyRate' => 550,
                'grossAnnualSalary' => 55000,
                'introduceYourself' => 'Introduce Intercontract 2 Company 1',
                'statusUpdatedAt' => $now->copy()->subDays(15),
                'skills' => [
                    $this->skills[2],
                    $this->skills[3],
                ],
                'softSkills' => [
                    $this->softSkills[2],
                    $this->softSkills[4],
                ],
                'formation' => [
                    'diplomaLevel' => 6,
                ],
                'reference' => 'C10002',
                'contact' => 'Contact Intercontract 2 Company 1',
                'nextAvailabilityAt' => null,
                'languages' => [
                    [
                        'language' => Language::LANGUAGE_FR,
                        'languageLevel' => LanguageLevel::NATIVE_OR_BILINGUAL,
                    ],
                    [
                        'language' => Language::LANGUAGE_RU,
                        'languageLevel' => LanguageLevel::NATIVE_OR_BILINGUAL,
                    ],
                ],
                'location' => $this->locations['lyon'],
                'locations' => [
                    $this->locations['lyon'],
                ],
                'documents' => [
                    [
                        'content' => 'Contenu CV Profile Intercontract 2 Company 1',
                    ],
                ],
                'createdBy' => $this->recruiters['walter.white@breaking-bad.com'],
            ],
            [
                'firstName' => 'FirstName Intercontract 1 Company 2',
                'lastName' => 'LastName Intercontract 1 Company 2',
                'profileJobTitle' => 'Profile Intercontract 1 Company 2',
                'experienceYear' => ExperienceYear::MORE_THAN_15_YEARS,
                'availability' => Availability::IMMEDIATE,
                'visible' => true,
                'averageDailyRate' => 750,
                'grossAnnualSalary' => 75000,
                'introduceYourself' => 'Introduce Intercontract 1 Company 2',
                'statusUpdatedAt' => $now->copy()->subDays(15),
                'skills' => [
                    $this->skills[4],
                ],
                'softSkills' => [
                    $this->softSkills[2],
                ],
                'formation' => [
                    'diplomaLevel' => 6,
                ],
                'reference' => 'C20001',
                'contact' => 'Contact Intercontract 1 Company 2',
                'nextAvailabilityAt' => null,
                'languages' => [
                    [
                        'language' => Language::LANGUAGE_FR,
                        'languageLevel' => LanguageLevel::NATIVE_OR_BILINGUAL,
                    ],
                ],
                'location' => $this->locations['paris'],
                'locations' => [
                    $this->locations['paris'],
                ],
                'documents' => [
                    [
                        'content' => 'Contenu CV Profile Intercontract 1 Company 2',
                    ],
                ],
                'createdBy' => $this->recruiters['robb.stark@got.com'],
            ],
        ];
    }

    public static function getGroups(): array
    {
        return ['user'];
    }

    public function getDependencies(): array
    {
        return [
            UserSkillsFixtures::class,
            UserLanguagesFixtures::class,
            SoftSkillsFixtures::class,
            UsersFixtures::class,
        ];
    }
}
