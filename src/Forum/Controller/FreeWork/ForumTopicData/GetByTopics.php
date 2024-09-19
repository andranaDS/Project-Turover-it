<?php

namespace App\Forum\Controller\FreeWork\ForumTopicData;

use App\Forum\Entity\ForumTopicData;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetByTopics
{
    /**
     * @Route(
     *     name="api_forum_freework_topic_datas_get_by_topics",
     *     path="/forum_topic_datas",
     *     methods={"GET"},
     *     condition= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
     * )
     * @Cache(smaxage="0", maxage="0")
     */
    public function __invoke(Request $request, EntityManagerInterface $em): Response
    {
        $ids = array_filter(explode(',', $request->query->get('ids', '')));
        $data = [];

        if (!empty($ids)) {
            $forumTopicDatas = $em->getRepository(ForumTopicData::class)->findBy(['id' => $ids]);
            foreach ($forumTopicDatas as $forumTopicData) {
                $data[$forumTopicData->getId()] = [
                    'repliesCount' => $forumTopicData->getRepliesCount(),
                    'viewsCount' => $forumTopicData->getViewsCount(),
                ];
            }
        }

        return new JsonResponse($data);
    }
}
