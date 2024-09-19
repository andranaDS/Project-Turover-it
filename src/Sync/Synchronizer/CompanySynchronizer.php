<?php

namespace App\Sync\Synchronizer;

use App\Company\Entity\Company;
use App\Company\Entity\CompanyBusinessActivity;
use App\Company\Entity\CompanyPicture;
use App\Company\Enum\CompanySize;
use App\Core\Entity\Location;
use App\Core\Entity\Skill;
use App\Core\Manager\LocationManager;
use App\Core\Util\Arrays;
use App\Core\Util\Files;
use App\Core\Util\Strings;
use App\Sync\Entity\SyncLog;
use App\Sync\Enum\SyncLogMode;
use App\Sync\Transformer\FileTransformer;
use App\Sync\Transformer\IntegerTransformer;
use App\Sync\Transformer\MatchTransformer;
use App\Sync\Transformer\SkillsTransformer;
use App\Sync\Transformer\StringTransformer;
use App\Sync\Transformer\TimestampTransformer;
use App\Sync\Transformer\UrlTransformer;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class CompanySynchronizer extends AbstractSynchronizer
{
    protected string $entity = Company::class;
    public array $skills;
    public array $businessActivities;
    public UploaderHelper $uploaderHelper;
    private LocationManager $lm;
    private PropertyAccessorInterface $propertyAccessor;

    private array $businessActivityValues = [
        100 => 1,
        101 => 2,
        102 => 3,
        103 => 4,
        104 => 5,
        105 => 6,
        106 => 7,
        107 => 8,
        108 => 9,
        109 => 10,
        110 => 11,
        111 => 12,
    ];
    private array $sizeValues = [
        165 => CompanySize::LESS_THAN_20_EMPLOYEES,
        166 => CompanySize::EMPLOYEES_20_99,
        167 => CompanySize::EMPLOYEES_100_249,
        168 => CompanySize::EMPLOYEES_250_999,
        169 => CompanySize::MORE_THAN_1000_EMPLOYEES,
    ];

    public function __construct(EntityManagerInterface $em, UploaderHelper $uploaderHelper, LocationManager $lm, PropertyAccessorInterface $propertyAccessor)
    {
        parent::__construct($em);

        // fetch skills
        $this->skills = [];
        foreach ($this->em->getRepository(Skill::class)->findAll() as $skill) {
            /* @var Skill $skill */
            $this->skills[mb_strtolower($skill->getName(), 'utf-8')] = $skill;
        }

        // fetch business activities
        $this->businessActivities = [];
        foreach ($this->em->getRepository(CompanyBusinessActivity::class)->findAll() as $businessActivity) {
            /* @var CompanyBusinessActivity $businessActivity */
            $this->businessActivities[$businessActivity->getId()] = $businessActivity;
        }

        $this->uploaderHelper = $uploaderHelper;
        $this->lm = $lm;
        $this->propertyAccessor = $propertyAccessor;
    }

    public function transform(array $inData, SynchronizableInterface $entity, array &$warnings = [], array &$errors = []): array
    {
        // name
        $tmpError = null;
        $outData['name'] = StringTransformer::transform($inData['name'], null, true, $tmpError);
        if (empty($tmpError) && !empty($outData['name']) && 1 !== preg_match('/[a-zA-Z]{1,}/', $outData['name'])) {
            $tmpError = sprintf('"%s" is not a valid string', $outData['name']);
        }
        $errors['name'] = $tmpError;

        // description
        $tmpError = null;
        $outData['description'] = StringTransformer::transform($inData['description'], null, false, $tmpError);
        $warnings['description'] = $tmpError;

        // annualRevenue
        $tmpError = null;
        $outData['annualRevenue'] = StringTransformer::transform($inData['annualRevenue'], null, false, $tmpError);
        $warnings['annualRevenue'] = $tmpError;

        // creationYear
        $tmpError = null;
        $outData['creationYear'] = IntegerTransformer::transform($inData['creationYear'], null, $tmpError);
        $warnings['creationYear'] = $tmpError;

        // websiteUrl
        $tmpError = null;
        $outData['websiteUrl'] = UrlTransformer::transform($inData['websiteUrl'], $tmpError);
        $warnings['websiteUrl'] = $tmpError;

        // linkedInUrl
        $tmpError = null;
        $outData['linkedInUrl'] = UrlTransformer::transform($inData['linkedInUrl'], $tmpError);
        $warnings['linkedInUrl'] = $tmpError;

        // twitterUrl
        $tmpError = null;
        $outData['twitterUrl'] = UrlTransformer::transform($inData['twitterUrl'], $tmpError);
        $warnings['twitterUrl'] = $tmpError;

        // facebookUrl
        $tmpError = null;
        $outData['facebookUrl'] = UrlTransformer::transform($inData['facebookUrl'], $tmpError);
        $warnings['facebookUrl'] = $tmpError;

        // size
        $tmpError = null;
        $outData['size'] = MatchTransformer::transform($inData['size'], $this->sizeValues, $tmpError);
        $warnings['size'] = $tmpError;

        // businessActivity
        $tmpError = null;
        $outData['businessActivity'] = MatchTransformer::transform($inData['businessActivity'], $this->businessActivityValues, $tmpError);
        $warnings['businessActivity'] = $tmpError;

        // logo
        $tmpError = null;
        $outData['logo'] = FileTransformer::transform($inData['logo'], '', $tmpError);
        $warnings['logo'] = $tmpError;

        // skills
        $tmpError = null;
        $outData['skills'] = SkillsTransformer::transform($inData['skills'], array_keys($this->skills), $tmpError);
        $warnings['skills'] = $tmpError;

        // pictures
        $pictures = [];
        foreach ([
            'gallery1',
            'gallery2',
            'gallery3',
            'gallery4',
        ] as $property) {
            $tmpError = null;
            $pictures[] = FileTransformer::transform($inData[$property], '', $tmpError);
            $warnings[$property] = $tmpError;
        }
        $outData['pictures'] = array_values(array_filter($pictures));

        // cover Picture
        $tmpError = null;
        $outData['coverPicture'] = FileTransformer::transform($inData['cover'], '', $tmpError);
        $warnings['coverPicture'] = $tmpError;

        // location
        $outData['location'] = null;
        $locationParts = array_filter(Arrays::subarray($inData, ['postalCode', 'locality', 'country']), static function (?string $e) {
            return !empty($e);
        });
        if (!empty($locationParts)) {
            $outData['location'] = Strings::defaultCase(implode(', ', $locationParts));
        }

        // directory
        $outData['directoryFreeWork'] = '1' === $inData['directory'];

        // createdAt
        $tmpError = null;
        $outData['createdAt'] = TimestampTransformer::transform($inData['createdAt'], $tmpError);
        $warnings['createdAt'] = $tmpError;

        $warnings = array_filter($warnings);
        $errors = array_filter($errors);

        return $outData;
    }

    public function hydrate(SynchronizableInterface $entity, array $outData, array &$warnings = [], array &$errors = []): SynchronizableInterface
    {
        if (!$entity instanceof Company) {
            throw new UnexpectedTypeException($entity, Company::class);
        }

        $entity
            ->setCreatedAt($outData['createdAt'])
            ->setName($outData['name'])
            ->setDescription($outData['description'])
            ->setExcerpt(null)
            ->setAnnualRevenue($outData['annualRevenue'])
            ->setCreationYear($outData['creationYear'])
            ->setWebsiteUrl($outData['websiteUrl'])
            ->setLinkedInUrl($outData['linkedInUrl'])
            ->setTwitterUrl($outData['twitterUrl'])
            ->setFacebookUrl($outData['facebookUrl'])
            ->setSize($outData['size'] ?? null)
            ->setBusinessActivity(null === $outData['businessActivity'] ? null : $this->businessActivities[$outData['businessActivity']])
            ->setDirectoryFreeWork($outData['directoryFreeWork'])
        ;

        // cover & logo
        $this->handleCompanyFile($warnings, $outData, $entity, 'logo');
        $this->handleCompanyFile($warnings, $outData, $entity, 'coverPicture');

        $oldPictures = [];
        $i = 1;
        foreach ($entity->getPictures() as $picture) {
            if (null === $url = $this->uploaderHelper->asset($picture, 'imageFile')) {
                continue;
            }

            try {
                $sha1 = @sha1_file($url);
            } catch (\ErrorException $e) {
                $warnings['currentPicture' . $i] = sprintf('"%s" was not found', $url);
                $entity->removePicture($picture);
                continue;
            }

            $oldPictures[$sha1] = [
                'url' => $url,
                'sha1' => $sha1,
                'object' => $picture,
            ];

            ++$i;
        }
        $oldPicturesSha1 = array_keys($oldPictures);

        $newPictures = [];
        foreach ($outData['pictures'] as $p) {
            $newPictures[$p['sha1']] = $p;
        }

        $newPicturesSha1 = array_keys($newPictures);

        $picturesSha1ToDelete = array_diff($oldPicturesSha1, $newPicturesSha1);
        foreach ($oldPictures as $picture) {
            if (null !== $picture['object'] && \in_array($picture['sha1'], $picturesSha1ToDelete, true)) {
                $entity->removePicture($picture['object']);
            }
        }

        $picturesSha1ToAdd = array_diff($newPicturesSha1, $oldPicturesSha1);
        $i = 1;
        foreach ($newPictures as $picture) {
            if (null !== $picture['url'] && \in_array($picture['sha1'], $picturesSha1ToAdd, true)) {
                if (null === $uploadedFile = Files::getUploadedFileFromAbsolutePath($picture['url'])) {
                    $warnings['newPicture' . $i] = sprintf('"%s" was not found', $picture['url']);
                    continue;
                }

                $companyPicture = (new CompanyPicture())
                    ->setImageFile($uploadedFile)
                ;
                $entity->addPicture($companyPicture);
            }
            ++$i;
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

        if (null !== $location = $entity->getLocation()) {
            $oldLocationValue = $location->getValue();
        } else {
            $oldLocationValue = null;
        }
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

    private function handleCompanyFile(array &$warnings, array &$outData, SynchronizableInterface $entity, string $fieldName): void
    {
        $currentUrl = $this->uploaderHelper->asset($entity, $fieldName . 'File');
        $currentSha1 = null;
        if (null !== $currentUrl) {
            try {
                $currentSha1 = @sha1_file($currentUrl);
            } catch (\ErrorException $e) {
                $warnings['current' . ucfirst($fieldName)] = sprintf('"%s" was not found', $currentUrl);
            }
        }

        $newUrl = $outData[$fieldName]['url'] ?? null;
        $newSha1 = $outData[$fieldName]['sha1'] ?? null;

        if (null === $newUrl) {
            $this->propertyAccessor->setValue($entity, $fieldName, null);
        } elseif ($newSha1 !== $currentSha1 && null !== $uploadedFile = Files::getUploadedFileFromAbsolutePath($newUrl)) {
            $this->propertyAccessor->setValue($entity, $fieldName . 'File', $uploadedFile);
        }
    }

    public function updateEntityIsNeeded(int $oldId, array $outData): bool
    {
        $lastSyncLog = $this->em->getRepository(SyncLog::class)->findOneBy([
            'oldCompanyId' => $oldId,
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
            $syncLog->setOldCompanyId($oldId);
        }

        if ($entity instanceof Company && true === $this->em->contains($entity)) {
            $syncLog->setNewCompany($entity);
        }
    }
}
