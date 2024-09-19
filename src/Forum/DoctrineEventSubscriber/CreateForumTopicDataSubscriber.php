<?php

namespace App\Forum\DoctrineEventSubscriber;

use App\Forum\Entity\ForumTopic;
use App\Forum\Entity\ForumTopicData;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class CreateForumTopicDataSubscriber implements EventSubscriber
{
    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
        ];
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $topic = $args->getObject();
        $em = $args->getObjectManager();

        if (!$topic instanceof ForumTopic) {
            return;
        }

        if (null === $topic->getId()) {
            return;
        }

        $forumTopicData = new ForumTopicData($topic->getId());

        $em->persist($forumTopicData);
        $em->flush();
    }
}
