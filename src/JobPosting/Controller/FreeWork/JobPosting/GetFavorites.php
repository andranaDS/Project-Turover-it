<?php

namespace App\JobPosting\Controller\FreeWork\JobPosting;

use App\JobPosting\Repository\JobPostingRepository;
use App\User\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class GetFavorites
{
    public function __invoke(Request $request, JobPostingRepository $jobPostingRepository, Security $security, NormalizerInterface $normalizer): array
    {
        $user = $security->getUser();

        if (!$user instanceof User) {
            throw new AuthenticationException();
        }

        return $jobPostingRepository->getUserFavorites($user);
    }
}
