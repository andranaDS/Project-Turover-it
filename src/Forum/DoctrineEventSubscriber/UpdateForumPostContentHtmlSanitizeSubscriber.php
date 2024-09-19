<?php

namespace App\Forum\DoctrineEventSubscriber;

use App\Forum\Entity\ForumPost;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use HtmlSanitizer\SanitizerInterface;
use Nette\Utils\Json;
use ProseMirror\ProseMirror;

class UpdateForumPostContentHtmlSanitizeSubscriber implements EventSubscriber
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
        $post = $args->getObject();

        if (!$post instanceof ForumPost) {
            return;
        }

        if ($args instanceof PreUpdateEventArgs && false === $args->hasChangedField('contentHtml')) {
            return;
        }

        $contentHtml = $post->getContentHtml();
        if ($contentHtml) {
            $sanitizedContent = $this->sanitizer->sanitize($contentHtml);
            if (null !== $sanitizedContent && '' !== $sanitizedContent) {
                $post->setContentHtml($sanitizedContent);
                $post->setContentJson(Json::encode(ProseMirror::htmlToJson($sanitizedContent)));
            }
        }
    }
}
