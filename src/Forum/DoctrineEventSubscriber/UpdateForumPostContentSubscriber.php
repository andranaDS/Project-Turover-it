<?php

namespace App\Forum\DoctrineEventSubscriber;

use App\Core\Util\Strings;
use App\Forum\Entity\ForumPost;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UpdateForumPostContentSubscriber implements EventSubscriber
{
    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $this->process($args);
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $this->process($args);
    }

    private function process(LifecycleEventArgs $args): void
    {
        $post = $args->getObject();

        if (!$post instanceof ForumPost) {
            return;
        }

        if ($args instanceof PreUpdateEventArgs && false === $args->hasChangedField('contentHtml')) {
            return;
        }

        $post->setContent(null === $post->getContentHtml() ? null : Strings::stripTags($post->getContentHtml()));
    }
}
