<?php

namespace App\Sync\Synchronizer;

use App\Sync\Entity\SyncLog;
use App\Sync\Enum\SyncLogMode;
use App\Sync\Enum\SyncLogSource;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractSynchronizer implements SynchronizerInterface
{
    protected string $entity;
    protected EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        if (empty($this->entity)) {
            throw new \InvalidArgumentException('The entity class must be defined');
        }

        if ((false !== $interfaces = class_implements($this->entity)) && (!\in_array(SynchronizableInterface::class, $interfaces, true))) {
            throw new \InvalidArgumentException(sprintf('Entity class "%s" does not implement "%s"', $this->entity, SynchronizableInterface::class));
        }

        $this->em = $em;
    }

    public function synchronize(array $inData, string $source = SyncLogSource::CRON): array
    {
        // defaults
        $processedAt = $entity = null;
        $outData = $warnings = $errors = [];

        $oldId = $this->getOldId($inData, $errors);
        $requestedAt = $this->getRequestedAt($inData, $errors);

        if (null !== $oldId && null !== $requestedAt && empty($errors)) {
            // oldId and requestedAt properties are valid
            $entity = $this->findOrCreate($oldId);

            // entity need to be updated
            $mode = null === $entity->getId() ? SyncLogMode::CREATE : SyncLogMode::UPDATE;
            $outData = $this->transform($inData, $entity, $warnings, $errors);

            if (empty($errors)) {
                // no blocking error detected after transform
                if ($this->updateEntityIsNeeded($oldId, $outData)) {
                    $entity = $this->hydrate($entity, $outData, $warnings, $errors);

                    if (empty($errors)) {
                        // no blocking error detected after hydrate
                        if (SyncLogMode::CREATE === $mode) {
                            $this->em->persist($entity);
                        }
                        $processedAt = new \DateTime();
                    } else {
                        $mode = SyncLogMode::FAIL;
                    }
                } else {
                    // entity does not need to be updated
                    $mode = SyncLogMode::SKIP;
                }
            } else {
                // blocking error detected after transform
                $entity = null;
                $mode = SyncLogMode::FAIL;
            }
        } else {
            // oldId and requestedAt properties are not valid
            $mode = SyncLogMode::FAIL;
        }

        // log
        if (SyncLogMode::SKIP !== $mode) {
            $syncLog = (new SyncLog())
                ->setSource($source)
                ->setInData(empty($inData) ? null : $inData)
                ->setOutData(empty($outData) ? null : $outData)
                ->setMode($mode)
                ->setRequestedAt($requestedAt)
                ->setProcessedAt($processedAt)
                ->setWarnings(empty($warnings) ? null : $warnings)
                ->setErrors(empty($errors) ? null : $errors)
            ;

            $this->updateSyncLog($syncLog, $entity, $oldId);

            $this->em->persist($syncLog);
        }

        return [
            'mode' => $mode,
            'entity' => $entity,
            'requestedAt' => $requestedAt,
        ];
    }

    public function findOrCreate(int $oldId): SynchronizableInterface
    {
        if (null === $entity = $this->find($oldId)) {
            $entity = $this->create($oldId);
        }

        return $entity;
    }

    public function find(int $oldId): ?SynchronizableInterface
    {
        return $this->em->getRepository($this->entity)->findOneByOldId($oldId); // @phpstan-ignore-line
    }

    public function create(int $oldId): SynchronizableInterface
    {
        $entity = new $this->entity();

        if (!$entity instanceof SynchronizableInterface) {
            throw new \InvalidArgumentException(sprintf('Entity "%s" does not implement "%s"', $this->entity, SynchronizableInterface::class));
        }

        return $entity->setOldId($oldId);
    }

    public function getOldId(array $inData, array &$errors = []): ?int
    {
        $oldId = $inData[$this->getOldIdPropertyName()] ?? null;

        if (null === $oldId || false === ctype_digit($oldId)) {
            $errors['oldId'] = sprintf('"%s" is an invalid integer', null === $oldId ? 'null' : $oldId);

            return null;
        }

        return (int) $oldId;
    }

    public function getRequestedAt(array $inData, array &$errors = []): ?\DateTimeInterface
    {
        if ((null !== $requestedAt = $inData[$this->getRequestedAtPropertyName()] ?? null) && (false !== ctype_digit($requestedAt))) {
            return (new \DateTime())->setTimestamp((int) $requestedAt);
        }

        $errors['requestedAt'] = sprintf('"%s" is an invalid timestamp', null === $requestedAt ? 'null' : $requestedAt);

        return null;
    }

    public function getOldIdPropertyName(): string
    {
        return 'id';
    }

    public function getRequestedAtPropertyName(): string
    {
        return 'updatedAt';
    }
}
