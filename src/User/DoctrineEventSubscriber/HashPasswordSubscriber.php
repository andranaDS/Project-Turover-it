<?php

namespace App\User\DoctrineEventSubscriber;

use App\User\Entity\User;
use App\User\Util\PasswordUpdaterInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class HashPasswordSubscriber implements EventSubscriber
{
    private PasswordUpdaterInterface $passwordUpdater;

    public function __construct(PasswordUpdaterInterface $passwordUpdater)
    {
        $this->passwordUpdater = $passwordUpdater;
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
        $object = $args->getObject();
        if ($object instanceof User) {
            $this->passwordUpdater->hashPassword($object);
        }
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();
        if ($object instanceof User) {
            $this->passwordUpdater->hashPassword($object);
        }
    }
}
