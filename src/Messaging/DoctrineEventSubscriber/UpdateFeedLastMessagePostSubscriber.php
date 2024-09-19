<?php

namespace App\Messaging\DoctrineEventSubscriber;

use App\Messaging\Entity\Feed;
use App\Messaging\Entity\Message;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UpdateFeedLastMessagePostSubscriber implements EventSubscriber
{
    private array $feeds = [];

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::postRemove,
            Events::postPersist,
            Events::postUpdate,
            Events::postFlush,
        ];
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->process($args);
    }

    public function postRemove(LifecycleEventArgs $args): void
    {
        $this->process($args);
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $this->process($args);
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        if (empty($this->feeds)) {
            return;
        }

        $em = $args->getObjectManager();

        foreach ($this->feeds as $feed) {
            /** @var Feed $feed */
            $lastMessage = $em->getRepository(Message::class)->findLastMessageByFeed($feed);

            $feed->setLastMessage($lastMessage);
        }

        $this->feeds = [];

        $em->flush();
    }

    private function process(LifecycleEventArgs $args): void
    {
        $message = $args->getObject();

        if (!$message instanceof Message
            || (null === $feed = $message->getFeed())
            || (null === $feedId = $feed->getId())
            || isset($this->feeds[$feedId])
        ) {
            return;
        }

        $this->feeds[$feedId] = $feed;
    }
}
