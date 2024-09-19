<?php

namespace App\Forum\Controller\FreeWork\ForumTopic;

use App\Forum\Entity\ForumTopic;
use App\Forum\Entity\ForumTopicTrace;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class Trace
{
    public function __invoke(EntityManagerInterface $em, ForumTopic $data): JsonResponse
    {
        $forumTopicTrace = (new ForumTopicTrace())
            ->setTopicId($data->getId())
        ;

        $em->persist($forumTopicTrace);
        $em->flush();

        return new JsonResponse([], Response::HTTP_CREATED);
    }
}
