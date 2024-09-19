<?php

namespace App\Sync\Synchronizer;

use App\Company\Entity\Company;
use App\Core\Entity\Location;
use App\Core\Entity\Skill;
use App\Core\Manager\LocationManager;
use App\Core\Util\Arrays;
use App\Core\Util\Strings;
use App\JobPosting\Entity\JobPosting;
use App\JobPosting\Enum\ApplicationType;
use App\JobPosting\Enum\Contract;
use App\JobPosting\Enum\RemoteMode;
use App\Sync\Entity\SyncLog;
use App\Sync\Enum\SyncLogMode;
use App\Sync\Transformer\CompanyTransformer;
use App\Sync\Transformer\JobPosting\DurationTransformer;
use App\Sync\Transformer\JobPosting\ExperienceLevelTransformer;
use App\Sync\Transformer\JobPosting\LocationTransformer;
use App\Sync\Transformer\JobPosting\RenewableTransformer;
use App\Sync\Transformer\JobPosting\SalaryTransformer;
use App\Sync\Transformer\JobPosting\StartsAtTransformer;
use App\Sync\Transformer\JobPosting\TitleTransformer;
use App\Sync\Transformer\MatchTransformer;
use App\Sync\Transformer\SkillsTransformer;
use App\Sync\Transformer\StringTransformer;
use App\Sync\Transformer\TimestampTransformer;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class JobPostingSynchronizer extends AbstractSynchronizer
{
    protected string $entity = JobPosting::class;
    public array $skills = [];
    private LocationManager $lm;
    private CompanyTransformer $ct;
    private array $remoteValues = [
        'no_teletravail' => RemoteMode::NONE,
        'teletravail_25' => RemoteMode::PARTIAL,
        'teletravail_partiel' => RemoteMode::PARTIAL,
        'teletravail_75' => RemoteMode::PARTIAL,
        'teletravail_total' => RemoteMode::FULL,
        '0' => RemoteMode::NONE,
        'teletravail_blanc' => RemoteMode::PARTIAL,
    ];
    private array $contractValues = [
        'alternance' => Contract::APPRENTICESHIP,
        'cdd' => Contract::FIXED_TERM,
        'CDD' => Contract::FIXED_TERM,
        'cdi' => Contract::PERMANENT,
        'CDI' => Contract::PERMANENT,
        'demande' => Contract::PERMANENT,
        'freelance' => Contract::CONTRACTOR,
        'interim' => Contract::FIXED_TERM,
        'intérim' => Contract::FIXED_TERM,
        'stage' => Contract::INTERNSHIP,
    ];

    public function __construct(EntityManagerInterface $em, LocationManager $lm, CompanyTransformer $ct)
    {
        parent::__construct($em);

        // fetch skills
        $this->skills = [];
        foreach ($this->em->getRepository(Skill::class)->findAll() as $skill) {
            /* @var Skill $skill */
            $this->skills[mb_strtolower($skill->getName(), 'utf-8')] = $skill;
        }

        $this->lm = $lm;
        $this->ct = $ct;
    }

    public function transform(array $inData, SynchronizableInterface $entity, array &$warnings = [], array &$errors = []): array
    {
        // name
        $tmpError = null;
        $outData['title'] = TitleTransformer::transform($inData['title'], $tmpError);
        $errors['title'] = $tmpError;

        // description
        $tmpError = null;
        $outData['description'] = StringTransformer::transform($inData['description'], null, true, $tmpError);
        $errors['description'] = $tmpError;

        // qualifications
        $tmpError = null;
        $outData['qualifications'] = StringTransformer::transform($inData['qualifications'], null, false, $tmpError);
        $errors['qualifications'] = $tmpError;

        // employerOverview
        $tmpError = null;
        $outData['employerOverview'] = StringTransformer::transform($inData['employerOverview'], null, false, $tmpError);
        $errors['employerOverview'] = $tmpError;

        // experienceLevel
        $tmpError = null;
        $outData['experienceLevel'] = ExperienceLevelTransformer::transform($inData['experienceLevel'], $tmpError);
        $warnings['experienceLevel'] = $tmpError;

        // salary
        $tmpError = null;
        $outDataSalary = [
            'minDailySalary' => null,
            'maxDailySalary' => null,
            'minAnnualSalary' => null,
            'maxAnnualSalary' => null,
        ];
        if (null !== $outDataSalaryTransformed = SalaryTransformer::transform($inData['salary'], $tmpError)) {
            $outDataSalary = array_merge($outDataSalary, $outDataSalaryTransformed);
        }
        $outData = array_merge($outData, $outDataSalary);
        $warnings['salary'] = $tmpError;

        // contract
        $tmpError = null;
        $outData['contract'] = MatchTransformer::transform($inData['contract'], $this->contractValues, $tmpError);

        $errors['contract'] = $tmpError;

        // duration
        $tmpError = null;
        $outData['duration'] = DurationTransformer::transform($inData['duration'], $tmpError);
        $warnings['duration'] = $tmpError;

        // renewable
        $tmpError = null;
        $outData['renewable'] = RenewableTransformer::transform($inData['duration']);

        // startsAt
        $tmpError = null;
        $outData['startsAt'] = StartsAtTransformer::transform($inData['startsAt']);
        $warnings['startsAt'] = $tmpError;

        // location
        $tmpError = null;
        if ('99' === $inData['locationCode']) {
            $outData['location'] = null;
        } else {
            $outData['location'] = Strings::defaultCase(LocationTransformer::transform($inData['location'], $tmpError) ?? 'France');
            $warnings['location'] = $tmpError;
        }

        // company
        $tmpError = null;
        $outData['company'] = $this->ct->transform($inData['mainCompanyId'] ?? $inData['companyId'] ?? null, true, $tmpError);
        $errors['company'] = $tmpError;

        // name
        $tmpError = null;
        $outData['reference'] = StringTransformer::transform($inData['reference'], null, false, $tmpError);
        $warnings['reference'] = $tmpError;

        // remoteMode
        if ('31' === $inData['locationCode']) {
            $outData['remoteMode'] = RemoteMode::FULL;
        } else {
            $tmpError = null;
            $outData['remoteMode'] = MatchTransformer::transform($inData['remoteMode'], $this->remoteValues, $tmpError);
            $warnings['remoteMode'] = $tmpError;
        }

        // publishedAt
        $publishedAt = null;

        $tmpError = null;
        if ('1' === $inData['published']) {
            $errors['publishedAt'] = $tmpError;
            $publishedAt = TimestampTransformer::transform($inData['publishedAt'], $tmpError);
        }

        $outData['publishedAt'] = $publishedAt;
        $outData['published'] = null !== $publishedAt;

        // skills
        $tmpError = null;
        $outData['skills'] = SkillsTransformer::transform($inData['skills'], array_keys($this->skills), $tmpError);
        $warnings['skills'] = $tmpError;

        // application type
        if (ApplicationType::CONTACT === $inData['applicationType'] && !empty($inData['applicationContact'])) {
            $outData['applicationType'] = ApplicationType::CONTACT;
            $outData['applicationContact'] = StringTransformer::transform($inData['applicationContact'], null, false, $tmpError);
        } elseif (ApplicationType::URL === $inData['applicationType'] && !empty($inData['applicationUrl']) && false !== filter_var($inData['applicationUrl'], \FILTER_VALIDATE_URL)) {
            $outData['applicationType'] = ApplicationType::URL;
            $outData['applicationUrl'] = StringTransformer::transform($inData['applicationUrl'], null, false, $tmpError);
        } else {
            $outData['applicationType'] = ApplicationType::TURNOVER;
        }

        $warnings = array_filter($warnings);
        $errors = array_filter($errors);

        return $outData;
    }

    public function hydrate(SynchronizableInterface $entity, array $outData, array &$warnings = [], array &$errors = []): SynchronizableInterface
    {
        if (!$entity instanceof JobPosting) {
            throw new UnexpectedTypeException($entity, Company::class);
        }

        $entity
            ->setTitle($outData['title'])
            ->setDescription($outData['description'])
            ->setCandidateProfile($outData['qualifications'])
            ->setCompanyDescription($outData['employerOverview'])
            ->setExperienceLevel($outData['experienceLevel'])
            ->setMinAnnualSalary($outData['minAnnualSalary'] ?? null)
            ->setMaxAnnualSalary($outData['maxAnnualSalary'] ?? null)
            ->setMinDailySalary($outData['minDailySalary'] ?? null)
            ->setMaxDailySalary($outData['maxDailySalary'] ?? null)
            ->setCurrency('EUR')
            ->setContracts(null === $outData['contract'] ? null : [$outData['contract']])
            ->setDurationPeriod(null !== $outData['duration'] ? $outData['duration'][DurationTransformer::INDEX_PERIOD] : null)
            ->setDurationValue(null !== $outData['duration'] ? $outData['duration'][DurationTransformer::INDEX_VALUE] : null)
            ->setStartsAt($outData['startsAt'])
            ->setPublished($outData['published'])
            ->setPublishedAt($outData['publishedAt'])
            ->setRemoteMode($outData['remoteMode'])
            ->setRenewable($outData['renewable'])
            ->setReference($outData['reference'])
            ->setApplicationType($outData['applicationType'])
            ->setApplicationContact($outData['applicationContact'] ?? null)
            ->setApplicationUrl($outData['applicationUrl'] ?? null)
        ;

        try {
            $company = $this->em->getReference(Company::class, $outData['company']);
            if ($company instanceof Company) {
                $entity->setCompany($company);
            }
        } catch (ORMException $e) {
            $errors['company'] = sprintf('"%s" was not found in the database', $outData['company']);
        }

        $oldSkillNames = [];
        foreach ($entity->getSkills() as $skill) {
            $oldSkillNames[] = mb_strtolower($skill->getName(), 'utf-8');
        }
        $newSkillNames = array_unique($outData['skills']);

        $skillNamesToDelete = array_diff($oldSkillNames, $newSkillNames);
        foreach ($skillNamesToDelete as $skillName) {
            $entity->removeSkill($this->skills[$skillName]);
        }

        $skillNamesToAdd = array_diff($newSkillNames, $oldSkillNames);
        foreach ($skillNamesToAdd as $skillName) {
            $entity->addSkill($this->skills[$skillName]);
        }

        $oldLocationValue = $entity->getLocation()->getValue();
        $newLocationValue = $outData['location'];

        if ($oldLocationValue !== $newLocationValue) {
            // update is needed
            $location = null;

            if (!empty($newLocationValue) && null === $location = ($this->lm->searchInDatabase($newLocationValue) ?? Arrays::first($this->lm->autocompleteMobilities($newLocationValue)))) {
                $warnings['location'] = sprintf('"%s" is not a found in the database and locationiq api', $newLocationValue);
            }

            if (null === $location) {
                $location = new Location();
            }

            $location->setValue($newLocationValue);
            $entity->setLocation($location);
        }

        return $entity;
    }

    public function updateEntityIsNeeded(int $oldId, array $outData): bool
    {
        $lastSyncLog = $this->em->getRepository(SyncLog::class)->findOneBy([
            'oldJobPostingId' => $oldId,
            'mode' => [SyncLogMode::CREATE, SyncLogMode::UPDATE, SyncLogMode::SKIP],
        ], [
            'requestedAt' => Criteria::DESC,
            'id' => Criteria::DESC,
        ]);

        if (null === $lastSyncLog) {
            return true;
        }

        try {
            return Json::encode($lastSyncLog->getOutData() ?? []) !== Json::encode($outData);
        } catch (JsonException $e) {
            return true;
        }
    }

    public function updateSyncLog(SyncLog $syncLog, ?SynchronizableInterface $entity, ?int $oldId): void
    {
        if (null !== $oldId) {
            $syncLog->setOldJobPostingId($oldId);
        }

        if ($entity instanceof JobPosting && true === $this->em->contains($entity)) {
            $syncLog->setNewJobPosting($entity);
        }
    }
}
