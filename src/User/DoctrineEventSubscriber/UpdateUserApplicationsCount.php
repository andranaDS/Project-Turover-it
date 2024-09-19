<?php

namespace App\User\DoctrineEventSubscriber;

use App\JobPosting\Entity\Application;
use App\User\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UpdateUserApplicationsCount implements EventSubscriber
{
    private array $users = [];

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
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
        if (empty($this->users)) {
            return;
        }

        $em = $args->getObjectManager();

        foreach ($this->users as $user) {
            /** @var User $user */
            $applicationCount = $em->getRepository(Application::class)->countByUser($user);
            $user->setApplicationsCount($applicationCount);
        }

        $this->users = [];

        $em->flush();
    }

    private function process(LifecycleEventArgs $args): void
    {
        $application = $args->getObject();

        if (
            !$application instanceof Application
            || (null === $user = $application->getUser())
            || (null === $userId = $user->getId())
        ) {
            return;
        }

        $this->users[$userId] = $user;
    }
}
