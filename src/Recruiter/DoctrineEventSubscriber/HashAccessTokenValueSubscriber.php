<?php

namespace App\Recruiter\DoctrineEventSubscriber;

use App\Recruiter\Entity\RecruiterAccessToken;
use App\Recruiter\Security\AccessTokenUtils;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class HashAccessTokenValueSubscriber implements EventSubscriber
{
    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();

        if (!$object instanceof RecruiterAccessToken) {
            return;
        }

        $plainTokenValue = $object->getPlainValue();
        if (empty($plainTokenValue)) {
            return;
        }

        $object
            ->setValue(AccessTokenUtils::hashTokenValue($plainTokenValue))
        ;
    }
}
