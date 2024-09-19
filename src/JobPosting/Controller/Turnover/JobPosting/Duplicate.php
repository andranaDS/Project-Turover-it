<?php

namespace App\JobPosting\Controller\Turnover\JobPosting;

use App\JobPosting\Entity\JobPosting;
use App\JobPosting\Enum\Status;
use App\Recruiter\Entity\Recruiter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;

class Duplicate
{
    public function __invoke(EntityManagerInterface $em, Security $security, JobPosting $data): JobPosting
    {
        if ((null === $recruiter = $security->getUser()) || !$recruiter instanceof Recruiter) {
            throw new AccessDeniedException();
        }

        $duplicatedJobPosting = (clone $data)
            ->setId(null)
            ->setSlug(null)
            ->setStatus(Status::DRAFT)
            ->setStartsAt(null)
            ->setReference(null)
            ->setPublishedAt(null)
            ->setAssignedTo($recruiter)
        ;

        $em->persist($duplicatedJobPosting);
        $em->flush();

        return $duplicatedJobPosting;
    }
}
