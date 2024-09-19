<?php

namespace App\Forum\DoctrineEventSubscriber;

use App\Forum\Entity\ForumTopic;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use HtmlSanitizer\SanitizerInterface;

class UpdateForumTopicTitleHtmlSanitizeSubscriber implements EventSubscriber
{
    private SanitizerInterface $sanitizer;

    public function __construct(SanitizerInterface $sanitizer)
    {
        $this->sanitizer = $sanitizer;
    }

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
        $topic = $args->getObject();

        if (!$topic instanceof ForumTopic) {
            return;
        }

        if ($args instanceof PreUpdateEventArgs && false === $args->hasChangedField('title')) {
            return;
        }

        $title = $topic->getTitle();
        if ($title) {
            $sanitizedTitle = $this->sanitizer->sanitize($title);
            $sanitizedTitle = html_entity_decode($sanitizedTitle, \ENT_QUOTES);
            $topic->setTitle($sanitizedTitle);
        }
    }
}
