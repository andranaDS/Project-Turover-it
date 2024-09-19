<?php

namespace App\Core\DoctrineEventSubscriber;

use App\Core\Entity\Alert;
use App\Core\Util\Strings;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UpdateAlertContentSubscriber implements EventSubscriber
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
        $alert = $args->getObject();

        if (!$alert instanceof Alert) {
            return;
        }

        if ($args instanceof PreUpdateEventArgs && false === $args->hasChangedField('contentHtml')) {
            return;
        }

        $alert->setContent(null === $alert->getContentHtml() ? null : Strings::stripTags($alert->getContentHtml()));
    }
}
