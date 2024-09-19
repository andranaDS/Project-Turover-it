<?php

namespace App\Core\Controller\FreeWork;

use App\Core\Entity\Config;
use App\Forum\Entity\ForumTopic;
use App\JobPosting\Entity\JobPosting;
use App\User\Entity\UserDocument;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeStatistics
{
    /**
     * @Route(
     *     name="api_core_freework_home_statistics_deprecated",
     *     path="/home_stats",
     *     methods={"GET"},
     *     condition= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
     * )
     * @Route(
     *     name="api_core_freework_home_statistics",
     *     path="/home/statistics",
     *     methods={"GET"},
     *     condition= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
     * )
     * @Cache(smaxage="7200", maxage="0")
     */
    public function __invoke(Request $request, EntityManagerInterface $em): JsonResponse
    {
        return new JsonResponse([
            'visibleResumeCount' => $em->getRepository(UserDocument::class)->countUserResume(),
            'jobPostingFreeCount' => $em->getRepository(JobPosting::class)->countFreeForHomeStats(),
            'jobPostingWorkCount' => $em->getRepository(JobPosting::class)->countWorkForHomeStats(),
            'turnoverItRecruitersCount' => $em->getRepository(Config::class)->getTurnoverItRecruitersCount(),
            'forumTopicsCount' => $em->getRepository(ForumTopic::class)->countAll(),
        ]);
    }
}
