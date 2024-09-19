<?php

namespace App\Core\Spam;

use App\Core\Repository\ForbiddenContentRepository;
use App\Core\Util\ContentDetector;

class ForbiddenContentDetector
{
    private ForbiddenContentRepository $repository;

    public function __construct(ForbiddenContentRepository $repository)
    {
        $this->repository = $repository;
    }

    public function isForbiddenValue(string $value, array &$detectedContents): bool
    {
        $contentsToDetect = $this->repository->findContents();
        if (empty($contentsToDetect)) {
            return false;
        }

        $detectedContents = ContentDetector::detect($value, $contentsToDetect);

        return !empty($detectedContents);
    }
}
