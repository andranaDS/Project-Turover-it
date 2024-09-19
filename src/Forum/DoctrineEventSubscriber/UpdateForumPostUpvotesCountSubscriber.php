<?php

namespace App\Forum\DoctrineEventSubscriber;

use App\Forum\Entity\ForumPost;
use App\Forum\Entity\ForumPostUpvote;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UpdateForumPostUpvotesCountSubscriber implements EventSubscriber
{
    private array $posts = [];

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
        if (empty($this->posts)) {
            return;
        }

        $em = $args->getObjectManager();

        foreach ($this->posts as $post) {
            /** @var ForumPost $post */
            if (null !== $post->getId()) {
                $upvotesCount = $em->getRepository(ForumPostUpvote::class)->countByPost($post);
                $post->setUpvotesCount($upvotesCount);
            }
        }

        $this->posts = [];

        $em->flush();
    }

    private function process(LifecycleEventArgs $args): void
    {
        $postUpvote = $args->getObject();

        if (
            !$postUpvote instanceof ForumPostUpvote
            || (null === $post = $postUpvote->getPost())
            || (null === $postId = $post->getId())
        ) {
            return;
        }

        $this->posts[$postId] = $post;
    }
}
