<?php

namespace App\Sync\Synchronizer;

use App\Sync\Entity\SyncLog;
use App\Sync\Enum\SyncLogSource;

interface SynchronizerInterface
{
    public function synchronize(array $inData, string $source = SyncLogSource::CRON): array;

    public function transform(array $inData, SynchronizableInterface $entity, array &$warnings = [], array &$errors = []): array;

    public function findOrCreate(int $oldId): SynchronizableInterface;

    public function find(int $oldId): ?SynchronizableInterface;

    public function create(int $oldId): SynchronizableInterface;

    public function hydrate(SynchronizableInterface $entity, array $outData, array &$warnings = [], array &$errors = []): SynchronizableInterface;

    public function getOldIdPropertyName(): string;

    public function getRequestedAtPropertyName(): string;

    public function getOldId(array $inData, array &$errors = []): ?int;

    public function getRequestedAt(array $inData, array &$errors = []): ?\DateTimeInterface;

    public function updateSyncLog(SyncLog $syncLog, ?SynchronizableInterface $entity, ?int $oldId): void;

    public function updateEntityIsNeeded(int $oldId, array $outData): bool;
}
