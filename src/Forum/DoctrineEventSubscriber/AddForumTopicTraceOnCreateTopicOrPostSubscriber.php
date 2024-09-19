<?php

namespace App\Forum\DoctrineEventSubscriber;

use App\Forum\Entity\ForumPost;
use App\Forum\Entity\ForumTopic;
use App\Forum\Entity\ForumTopicTrace;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class AddForumTopicTraceOnCreateTopicOrPostSubscriber implements EventSubscriber
{
    private array $posts = [];

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postFlush,
        ];
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->process($args);
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        if (empty($this->posts)) {
            return;
        }

        $em = $args->getObjectManager();

        foreach ($this->posts as $post) {
            /* @var ForumPost|ForumTopic $post */
            $user = $post->getAuthor();
            $topic = ($post instanceof ForumPost) ? $post->getTopic() : $post;

            if ($post instanceof ForumPost && $topic->getInitialPost() === $post) {
                continue;
            }

            $forumTopicTrace = new ForumTopicTrace();
            $forumTopicTrace
                ->setUser($user)
                ->setTopicId($topic->getId())
                ->setCreated(true)
            ;

            $em->persist($forumTopicTrace);
        }

        $this->posts = [];

        $em->flush();
    }

    private function process(LifecycleEventArgs $args): void
    {
        $post = $args->getObject();

        if ($post instanceof ForumPost || $post instanceof ForumTopic) {
            $this->posts[] = $post;
        }
    }
}
