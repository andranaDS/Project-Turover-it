<?php

namespace App\JobPosting\Controller\FreeWork\JobPosting;

use App\JobPosting\Entity\JobPosting;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetLegacy
{
    public function __invoke(int $oldId, EntityManagerInterface $em): JobPosting
    {
        if (null === $jobPosting = $em->getRepository(JobPosting::class)->findOneByOldId($oldId)) {
            throw new NotFoundHttpException();
        }

        return $jobPosting;
    }
}
