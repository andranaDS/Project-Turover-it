<?php

namespace App\Forum\DoctrineEventSubscriber;

use App\Forum\Entity\ForumTopic;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UpdateForumTopicInitialPostSubscriber implements EventSubscriber
{
    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $topic = $args->getObject();

        if (!$topic instanceof ForumTopic) {
            return;
        }

        $topic->setInitialPost($topic->getPosts()->first());
    }
}
