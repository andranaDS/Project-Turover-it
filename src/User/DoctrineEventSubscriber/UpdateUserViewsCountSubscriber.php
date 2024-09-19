<?php

namespace App\User\DoctrineEventSubscriber;

use App\User\Entity\User;
use App\User\Entity\UserTrace;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UpdateUserViewsCountSubscriber implements EventSubscriber
{
    // Users to count
    private array $users = [];

    /**
     * Subscribed events.
     *
     * @return array|string[]
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postFlush,
        ];
    }

    /**
     * After we persist our data.
     */
    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->process($args);
    }

    /**
     * After we flush our data.
     */
    public function postFlush(PostFlushEventArgs $args): void
    {
        if (empty($this->users)) {
            return;
        }

        $em = $args->getObjectManager();

        foreach ($this->users as $user) {
            /** @var User $user */
            $usersCount = $em->getRepository(UserTrace::class)->countByUser($user);
            $user->setViewsCount($usersCount);
        }

        $this->users = [];
        $em->flush();
    }

    /**
     * Process.
     */
    public function process(LifecycleEventArgs $args): void
    {
        $userTrace = $args->getObject();

        if (!$userTrace instanceof UserTrace || (null === $user = $userTrace->getUser())) {
            return;
        }

        $this->users[$user->getId()] = $user;
    }
}
