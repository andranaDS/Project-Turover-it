<?php

namespace App\Sync\Synchronizer;

interface SynchronizableInterface
{
    public function getId(): ?int;

    public function getOldId(): ?int;

    public function setOldId(?int $oldId): self;
}
