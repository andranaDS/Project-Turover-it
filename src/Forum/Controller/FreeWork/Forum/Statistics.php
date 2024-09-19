<?php

namespace App\Forum\Controller\FreeWork\Forum;

use App\Forum\Manager\ForumStatisticsManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Statistics
{
    /**
     * @Route(
     *     name="api_forum_freework_statistics",
     *     path="/forum/statistics",
     *     methods={"GET"},
     *     condition= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
     * )
     * @Cache(smaxage="300", maxage="0")
     */
    public function __invoke(ForumStatisticsManager $fsm): Response
    {
        return new JsonResponse($fsm->getGlobalData());
    }
}
