<?php

namespace App\Blog\DoctrineEventSubscriber;

use App\Blog\Entity\BlogPost;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UpdateBlogPostReadingTimeMinutesSubscriber implements EventSubscriber
{
    private array $posts = [];

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::postUpdate,
            Events::postPersist,
            Events::postFlush,
        ];
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->process($args);
    }

    public function postUpdate(LifecycleEventArgs $args): void
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
            /* @var BlogPost $post */
            $post->setReadingTimeMinutes(ceil(str_word_count($post->getContent()) / 250));
        }

        $this->posts = [];

        $em->flush();
    }

    private function process(LifecycleEventArgs $args): void
    {
        $post = $args->getObject();

        if (
            !$post instanceof BlogPost
            || (null === $postId = $post->getId())
        ) {
            return;
        }

        $this->posts[$postId] = $post;
    }
}
