<?php

namespace App\JobPosting\DataFixtures;

use App\Company\DataFixtures\CompaniesFixtures;
use App\Company\Entity\Company;
use App\Core\DataFixtures\AbstractFixture;
use App\Core\DataFixtures\JobFixtures;
use App\Core\DataFixtures\SkillsFixtures;
use App\Core\DataFixtures\SoftSkillsFixtures;
use App\Core\Entity\Location;
use App\Core\Entity\Skill;
use App\Core\Entity\SoftSkill;
use App\Core\Util\Arrays;
use App\JobPosting\Entity\JobPosting;
use App\JobPosting\Enum\ApplicationType;
use App\JobPosting\Enum\Status;
use App\Recruiter\DataFixtures\RecruiterFixtures;
use App\Recruiter\Entity\Recruiter;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Yaml\Yaml;

class JobPostingsFixtures extends AbstractFixture implements DependentFixtureInterface
{
    private array $companies = [];
    private array $skills = [];
    private array $softSkills = [];
    private array $recruiters = [];
    private DenormalizerInterface $denormalizer;

    public function __construct(string $env, DenormalizerInterface $denormalizer)
    {
        $this->denormalizer = $denormalizer;
        parent::__construct($env);
    }

    public function load(ObjectManager $manager): void
    {
        // fetch companies
        $companies = $manager->getRepository(Company::class)->findSome();

        foreach ($companies as $company) {
            /* @var Company $company */
            $this->companies[$company->getId()] = $company;
        }

        // fetch skills
        $skills = $manager->getRepository(Skill::class)->findAll();
        foreach ($skills as $skill) {
            /* @var Skill $skill */
            $this->skills[$skill->getSlug()] = $skill;
        }

        // fetch softSkills
        $softSkills = $manager->getRepository(SoftSkill::class)->findAll();
        foreach ($softSkills as $softSkill) {
            /* @var SoftSkill $softSkill */
            $this->softSkills[$softSkill->getSlug()] = $softSkill;
        }

        // fetch recruiters
        $recruiters = $manager->getRepository(Recruiter::class)->findAll();
        foreach ($recruiters as $recruiter) {
            /* @var Recruiter $recruiter */
            $this->recruiters[$recruiter->getEmail()] = $recruiter;
        }

        $data = $this->getData();

        $oldId = 10000;
        foreach ($data as $d) {
            $jobPosting = (new JobPosting())
                ->setTitle($d['title'])
                ->setDescription($d['description'])
                ->setCandidateProfile($d['candidateProfile'] ?? null)
                ->setCompanyDescription($d['companyDescription'] ?? null)
                ->setExperienceLevel($d['experienceLevel'])
                ->setMinAnnualSalary($d['minAnnualSalary'])
                ->setMaxAnnualSalary($d['maxAnnualSalary'])
                ->setMinDailySalary($d['minDailySalary'])
                ->setMaxDailySalary($d['maxDailySalary'])
                ->setCurrency($d['currency'])
                ->setContracts($d['contracts'])
                ->setRenewable($d['renewable'])
                ->setDurationPeriod($d['durationPeriod'] ?? null)
                ->setDurationValue($d['durationValue'] ?? null)
                ->setRemoteMode($d['remoteMode'])
                ->setStartsAt($d['startsAt'])
                ->setCompany($d['company'])
                ->setCreatedAt($d['createdAt'])
                ->setUpdatedAt($d['createdAt'])
                ->setPublishedAt($d['createdAt'])
                ->setPublished(null !== $d['createdAt'])
                ->setLocation($d['location'])
                ->setOldId(++$oldId)
                ->setApplicationType(ApplicationType::TURNOVER)
                ->setApplicationEmail('zzidane@free-work.fr')
                ->setApplicationContact(null)
                ->setApplicationUrl(null)
                ->setMulticast($d['multicast'] ?? true)
                ->setStatus($d['status'] ?? Status::PUBLISHED)
                ->setViewsCount($d['viewsCount'] ?? 9)
                ->setDaysOnlineCount($d['daysOnlineCount'] ?? 9)
                ->setPushToTop($d['pushToTop'] ?? 0)
                ->setPushedToTopCount(0)
                ->setQuality($d['quality'] ?? 10)
                ->setCreatedBy($d['createdBy'])
                ->setAssignedTo($d['assignedTo'])
                ->setReference($d['reference'] ?? null)
            ;

            foreach (($d['skills'] ?? []) as $skill) {
                $jobPosting->addSkill($skill);
            }

            foreach (($d['softSkills'] ?? []) as $softSkill) {
                $jobPosting->addSoftSkill($softSkill);
            }

            $manager->persist($jobPosting);
        }

        $manager->flush();
    }

    public function getDevData(): array
    {
        $jobs = Yaml::parseFile(__DIR__ . '/data/job_postings_dev.yaml');

        return $this->prepareJobs($jobs);
    }

    public function getTestData(): array
    {
        $jobs = Yaml::parseFile(__DIR__ . '/data/job_postings_test.yaml');

        return $this->prepareJobs($jobs);
    }

    private function prepareJobs(array $jobs): array
    {
        foreach ($jobs as &$job) {
            if ($job['createdAt']) {
                if ('recent' === $job['createdAt']) {
                    $job['createdAt'] = (new \DateTime())->setTime(0, 30);
                } elseif ('yesterday' === $job['createdAt']) {
                    $job['createdAt'] = (new \DateTime())->modify('-1 day')->setTime(8, 00);
                } else {
                    $job['createdAt'] = new \DateTime($job['createdAt']);
                }
            }

            if ($job['startsAt']) {
                $job['startsAt'] = new \DateTime($job['startsAt']);
            }

            if ($job['location']) {
                $job['location'] = $this->denormalizer->denormalize($job['location'], Location::class);
            }

            if ($job['company']) {
                $job['company'] = $this->companies[$job['company']];
            }

            if (isset($job['skills']) && !empty($job['skills'])) {
                $job['skills'] = Arrays::subarray($this->skills, $job['skills']);
            }

            if (isset($job['softSkills']) && !empty($job['softSkills'])) {
                $job['softSkills'] = Arrays::subarray($this->softSkills, $job['softSkills']);
            }

            if (isset($job['createdBy'])) {
                $job['createdBy'] = $this->recruiters[$job['createdBy']];
            }

            if (isset($job['assignedTo'])) {
                $job['assignedTo'] = $this->recruiters[$job['assignedTo']];
            }
        }

        return $jobs;
    }

    public function getDependencies()
    {
        return [
            CompaniesFixtures::class,
            RecruiterFixtures::class,
            SkillsFixtures::class,
            SoftSkillsFixtures::class,
            JobFixtures::class,
        ];
    }
}
