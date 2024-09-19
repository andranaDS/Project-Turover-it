<?php

namespace App\Blog\DoctrineEventSubscriber;

use App\Blog\Entity\BlogPost;
use App\Core\Util\Strings;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UpdateBlogPostContentSubscriber implements EventSubscriber
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

        if (!$post instanceof BlogPost) {
            return;
        }

        if ($args instanceof PreUpdateEventArgs && false === $args->hasChangedField('contentHtml')) {
            return;
        }

        $post->setContent(null === $post->getContentHtml() ? null : Strings::stripTags($post->getContentHtml()));
    }
}
