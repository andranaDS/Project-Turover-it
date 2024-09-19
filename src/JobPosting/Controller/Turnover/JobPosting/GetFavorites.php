<?php

namespace App\JobPosting\Controller\Turnover\JobPosting;

use App\JobPosting\Repository\JobPostingRepository;
use App\Recruiter\Entity\Recruiter;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class GetFavorites
{
    public function __invoke(Request $request, JobPostingRepository $jobPostingRepository, Security $security, NormalizerInterface $normalizer): array
    {
        $recruiter = $security->getUser();

        if (!$recruiter instanceof Recruiter) {
            throw new BadRequestException();
        }

        return $jobPostingRepository->getRecruiterFavorites($recruiter);
    }
}
