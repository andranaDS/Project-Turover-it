<?php

namespace App\Forum\Controller\FreeWork\ForumCategory;

use App\Forum\Entity\ForumCategory;
use App\Forum\Entity\ForumTopicTrace;
use Doctrine\ORM\EntityManagerInterface;

class Trace
{
    public function __invoke(EntityManagerInterface $em, ForumCategory $data): ForumCategory
    {
        foreach ($data->getTopics() as $topic) {
            $forumTopicTrace = (new ForumTopicTrace())
                ->setTopicId($topic->getId())
                ->setMarkAllAsRead(true)
            ;
            $em->persist($forumTopicTrace);
        }

        $em->flush();

        return $data;
    }
}
