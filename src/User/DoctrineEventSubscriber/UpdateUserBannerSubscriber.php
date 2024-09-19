<?php

namespace App\User\DoctrineEventSubscriber;

use App\User\Entity\BanUser;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UpdateUserBannerSubscriber implements EventSubscriber
{
    private array $users = [];

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postRemove,
            Events::postFlush,
        ];
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->process($args, 'persist');
    }

    public function postRemove(LifecycleEventArgs $args): void
    {
        $this->process($args, 'remove');
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        if (empty($this->users)) {
            return;
        }

        $em = $args->getObjectManager();

        foreach ($this->users as $actionUser) {
            $actionUser['user']->setBanned('persist' === $actionUser['action']);
        }

        $this->users = [];
        $em->flush();
    }

    private function process(LifecycleEventArgs $args, string $action = ''): void
    {
        $banUser = $args->getObject();

        if ((!$banUser instanceof BanUser) || (null === $user = $banUser->getUser())) {
            return;
        }

        $this->users[] = ['action' => $action, 'user' => $user];
    }
}
