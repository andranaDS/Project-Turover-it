<?php

namespace App\Forum\DoctrineEventSubscriber;

use App\Forum\Entity\ForumPost;
use App\Forum\Entity\ForumTopic;
use App\Forum\Entity\ForumTopicData;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UpdateForumTopicPostsCountSubscriber implements EventSubscriber
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
        if (empty($this->topics)) {
            return;
        }

        $em = $args->getObjectManager();

        foreach ($this->topics as $topic) {
            /** @var ForumTopic $topic */
            $topicData = $em->getRepository(ForumTopicData::class)->findOneById($topic->getId());
            $postsCount = $em->getRepository(ForumPost::class)->countByTopic($topic);
            $repliesCount = (int) max(0, $postsCount - 1);

            if (null !== $topicData) {
                $topicData
                    ->setPostsCount($postsCount)
                    ->setRepliesCount($repliesCount)
                ;
            }
        }

        $this->topics = [];

        $em->flush();
    }

    private function process(LifecycleEventArgs $args): void
    {
        $post = $args->getObject();

        if (!$post instanceof ForumPost
            || (null === $topic = $post->getTopic())
            || (null === $topicId = $topic->getId())
            || isset($this->topics[$topicId])
        ) {
            return;
        }

        $this->topics[$topicId] = $topic;
    }
}