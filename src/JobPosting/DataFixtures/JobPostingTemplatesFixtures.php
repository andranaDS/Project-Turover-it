<?php

namespace App\JobPosting\DataFixtures;

use App\Core\DataFixtures\AbstractFixture;
use App\Core\DataFixtures\SkillsFixtures;
use App\Core\DataFixtures\SoftSkillsFixtures;
use App\Core\Entity\Location;
use App\Core\Entity\Skill;
use App\Core\Entity\SoftSkill;
use App\Core\Enum\Currency;
use App\Core\Util\Arrays;
use App\JobPosting\Entity\JobPostingTemplate;
use App\JobPosting\Enum\ApplicationType;
use App\JobPosting\Enum\Contract;
use App\JobPosting\Enum\RemoteMode;
use App\Recruiter\DataFixtures\RecruiterFixtures;
use App\Recruiter\Entity\Recruiter;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class JobPostingTemplatesFixtures extends AbstractFixture implements DependentFixtureInterface
{
    private array $skills = [];
    private array $softSkills = [];
    private array $recruiters = [];
    private array $locations = [];
    private DenormalizerInterface $denormalizer;

    public function __construct(string $env, DenormalizerInterface $denormalizer)
    {
        parent::__construct($env);
        $this->denormalizer = $denormalizer;
    }

    public function load(ObjectManager $manager): void
    {
        // fetch skills
        $skills = $manager->getRepository(Skill::class)->findAll();
        foreach ($skills as $skill) {
            /* @var Skill $skill */
            $this->skills[$skill->getSlug()] = $skill;
        }

        // fetch soft skills
        $softSkills = $manager->getRepository(SoftSkill::class)->findAll();
        foreach ($softSkills as $softSkill) {
            /* @var SoftSkill $softSkill */
            $this->softSkills[$softSkill->getSlug()] = $softSkill;
        }

        // fetch recruiters
        $recruiters = $manager->getRepository(Recruiter::class)->findAll();
        foreach ($recruiters as $recruiter) {
            $this->recruiters[$recruiter->getEmail()] = $recruiter;
        }

        // fetch locations
        $this->locations = array_map(static function (string $location) {
            return json_decode($location, true, 512, \JSON_THROW_ON_ERROR);
        }, [
            'lyon' => '{"subLocality":null,"locality":"Lyon","localitySlug":"lyon","postalCode":null,"adminLevel1":"Auvergne-Rhône-Alpes","adminLevel1Slug":"auvergne-rhone-alpes","adminLevel2":"Métropole de Lyon","adminLevel2Slug":"metropole-de-lyon","country":"France","countryCode":"FR","latitude":"45.7578137","longitude":"4.8320114"}',
            'paris' => '{"subLocality":"Paris","locality":"Paris","localitySlug":"paris","postalCode":null,"adminLevel1":"Île-de-France","adminLevel1Slug":"ile-de-france","adminLevel2":null,"adminLevel2Slug":null,"country":"France","countryCode":"FR","latitude":"48.8588897","longitude":"2.3200410217201"}',
            'idf' => '{"subLocality":null,"locality":null,"localitySlug":null,"postalCode":null,"adminLevel1":"Île-de-France","adminLevel1Slug":"ile-de-france","adminLevel2":null,"adminLevel2Slug":null,"country":"France","countryCode":"FR","latitude":"48.6443057","longitude":"2.7537863"}',
        ]);

        foreach ($this->getData() as $data) {
            $template = (new JobPostingTemplate())
                ->setTitle($data['title'])
                ->setMinAnnualSalary($data['minAnnualSalary'])
                ->setMaxAnnualSalary($data['maxAnnualSalary'])
                ->setMinDailySalary($data['minDailySalary'])
                ->setMaxDailySalary($data['maxDailySalary'])
                ->setCurrency($data['currency'])
                ->setContracts($data['contracts'])
                ->setLocation($this->denormalizer->denormalize($data['location'], Location::class))
                ->setDurationPeriod($data['durationPeriod'])
                ->setDurationValue($data['durationValue'])
                ->setApplicationType(ApplicationType::TURNOVER)
                ->setCreatedAt($data['createdAt'])
                ->setUpdatedAt($data['createdAt'])
                ->setCreatedBy($data['createdBy'])
            ;

            foreach ($data['skills'] ?? [] as $skill) {
                $template->addSkill($skill);
            }

            foreach ($data['softSkills'] ?? [] as $softSkill) {
                $template->addSoftSkill($softSkill);
            }

            $manager->persist($template);
        }

        $manager->flush();
    }

    public function getData(): array
    {
        return [
            [
                'title' => 'Développeur Web',
                'minAnnualSalary' => 45000,
                'maxAnnualSalary' => 55000,
                'minDailySalary' => null,
                'maxDailySalary' => null,
                'currency' => Currency::EUR,
                'contracts' => [Contract::PERMANENT],
                'remoteMode' => RemoteMode::PARTIAL,
                'durationPeriod' => 'day',
                'durationValue' => 1,
                'location' => $this->locations['paris'],
                'skills' => Arrays::subarray($this->skills, ['php', 'javascript']),
                'softSkills' => Arrays::subarray($this->softSkills, ['softskill-1', 'softskill-2']),
                'createdAt' => new \DateTime('2022-01-01 12:00:00'),
                'createdBy' => $this->recruiters['eddard.stark@got.com'],
            ],
            [
                'title' => 'Lead développeur',
                'description' => null,
                'minAnnualSalary' => null,
                'maxAnnualSalary' => null,
                'minDailySalary' => 250,
                'maxDailySalary' => 400,
                'currency' => Currency::USD,
                'contracts' => [Contract::INTERCONTRACT],
                'remoteMode' => RemoteMode::FULL,
                'durationPeriod' => 'month',
                'durationValue' => 45,
                'location' => $this->locations['paris'],
                'createdAt' => new \DateTime('2022-01-01 13:00:00'),
                'createdBy' => $this->recruiters['eddard.stark@got.com'],
            ],
            [
                'title' => 'Développeur NodeJS',
                'minAnnualSalary' => 30000,
                'maxAnnualSalary' => 35000,
                'minDailySalary' => 300,
                'maxDailySalary' => 350,
                'currency' => Currency::USD,
                'contracts' => [Contract::CONTRACTOR, Contract::FIXED_TERM],
                'remoteMode' => RemoteMode::NONE,
                'durationPeriod' => 'day',
                'durationValue' => 10,
                'location' => $this->locations['paris'],
                'softSkills' => Arrays::subarray($this->softSkills, ['softskill-3']),
                'createdAt' => new \DateTime('2022-01-01 14:00:00'),
                'createdBy' => $this->recruiters['eddard.stark@got.com'],
            ],
            [
                'title' => 'Data Analyst',
                'minAnnualSalary' => 35000,
                'maxAnnualSalary' => 38000,
                'minDailySalary' => null,
                'maxDailySalary' => null,
                'currency' => Currency::GBP,
                'contracts' => [Contract::PERMANENT],
                'remoteMode' => RemoteMode::FULL,
                'durationPeriod' => 'year',
                'durationValue' => 2,
                'skills' => Arrays::subarray($this->skills, ['php', 'javascript']),
                'location' => $this->locations['lyon'],
                'createdAt' => new \DateTime('2022-01-01 15:00:00'),
                'createdBy' => $this->recruiters['eddard.stark@got.com'],
            ],
            [
                'title' => 'Consultant Reflex',
                'minAnnualSalary' => 35000,
                'maxAnnualSalary' => 40000,
                'minDailySalary' => null,
                'maxDailySalary' => null,
                'currency' => Currency::GBP,
                'contracts' => [Contract::FIXED_TERM],
                'remoteMode' => RemoteMode::NONE,
                'durationPeriod' => 'year',
                'durationValue' => 1,
                'location' => $this->locations['idf'],
                'skills' => Arrays::subarray($this->skills, ['php', 'laravel', 'symfony', 'docker']),
                'softSkills' => Arrays::subarray($this->softSkills, ['softskill-1', 'softskill-2']),
                'createdAt' => new \DateTime('2022-01-01 16:00:00'),
                'createdBy' => $this->recruiters['carrie.mathison@homeland.com'],
            ],
            [
                'title' => 'Administrateur BDD',
                'minAnnualSalary' => 45000,
                'maxAnnualSalary' => null,
                'minDailySalary' => null,
                'maxDailySalary' => null,
                'currency' => Currency::EUR,
                'contracts' => [Contract::PERMANENT],
                'remoteMode' => RemoteMode::PARTIAL,
                'startsAt' => '2020-08-16T00:00:00+00:00',
                'durationPeriod' => 'month',
                'durationValue' => 36,
                'location' => $this->locations['idf'],
                'skills' => Arrays::subarray($this->skills, ['php', 'symfony']),
                'createdAt' => new \DateTime('2022-01-01 17:00:00'),
                'createdBy' => $this->recruiters['carrie.mathison@homeland.com'],
            ],
        ];
    }

    public function getDependencies()
    {
        return [
            RecruiterFixtures::class,
            SoftSkillsFixtures::class,
            SkillsFixtures::class,
        ];
    }
}
