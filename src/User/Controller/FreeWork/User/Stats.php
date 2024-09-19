<?php

namespace App\User\Controller\FreeWork\User;

use App\JobPosting\ElasticSearch\Pagination\JobPostingsPaginator;
use App\JobPosting\Entity\Application;
use App\JobPosting\Entity\JobPosting;
use App\JobPosting\Enum\ApplicationStep;
use App\User\Entity\User;
use App\User\Entity\UserProfileViews;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class Stats
{
    public function __invoke(User $data, JobPostingsPaginator $paginator, EntityManagerInterface $em): Response
    {
        return new JsonResponse([
            'userProfileViews' => (int) $em->getRepository(UserProfileViews::class)->sumSince($data, (new \DateTime())->modify('-7 days')->setTime(0, 0)),
            'applicationsCount' => $em->getRepository(Application::class)->count([
                'user' => $data,
                'step' => [ApplicationStep::RESUME, ApplicationStep::SEEN],
            ]),
            'jobPostingSuggestedCount' => $em->getRepository(JobPosting::class)->countUserSuggested($data),
            'todayJobPostingFreeCount' => $em->getRepository(JobPosting::class)->countFreeForHomeStats(Carbon::today()),
            'todayJobPostingWorkCount' => $em->getRepository(JobPosting::class)->countWorkForHomeStats(Carbon::today()),
        ]);
    }
}
