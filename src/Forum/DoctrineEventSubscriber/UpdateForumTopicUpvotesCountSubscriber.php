<?php

namespace App\Forum\DoctrineEventSubscriber;

use App\Forum\Entity\ForumPostUpvote;
use App\Forum\Entity\ForumTopic;
use App\Forum\Entity\ForumTopicData;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UpdateForumTopicUpvotesCountSubscriber implements EventSubscriber
{
    private array $topics = [];

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::postRemove,
            Events::postPersist,
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

    public function postFlush(PostFlushEventArgs $args): void
    {
        if (empty($this->topics)) {
            return;
        }

        $em = $args->getObjectManager();

        foreach ($this->topics as $topic) {
            /** @var ForumTopic $topic */
            $topicData = $em->getRepository(ForumTopicData::class)->findOneById($topic->getId());
            $upvotesCount = $em->getRepository(ForumPostUpvote::class)->countByTopic($topic);
            if (null !== $topicData) {
                $topicData->setUpvotesCount($upvotesCount);
            }
        }

        $this->topics = [];

        $em->flush();
    }

    private function process(LifecycleEventArgs $args): void
    {
        $postUpvote = $args->getObject();

        if (
            !$postUpvote instanceof ForumPostUpvote
            || (null === $post = $postUpvote->getPost())
            || (null === $topic = $post->getTopic())
            || (null === $topicId = $topic->getId())
        ) {
            return;
        }

        $this->topics[$topicId] = $topic;
    }
}
