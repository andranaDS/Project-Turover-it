<?php

namespace App\Messaging\DoctrineEventSubscriber;

use App\Core\Util\Strings;
use App\Messaging\Entity\Message;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UpdateMessageContentSubscriber implements EventSubscriber
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
        $message = $args->getObject();

        if (!$message instanceof Message) {
            return;
        }

        if ($args instanceof PreUpdateEventArgs && false === $args->hasChangedField('contentHtml')) {
            return;
        }

        $message->setContent(null === $message->getContentHtml() ? null : Strings::stripTags($message->getContentHtml()));
    }
}
