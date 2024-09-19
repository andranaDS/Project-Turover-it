<?php

namespace App\Forum\DoctrineEventSubscriber;

use App\Forum\Entity\ForumPost;
use App\User\Contracts\UserInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UpdateUserPostsCountSubscriber implements EventSubscriber
{
    private array $users = [];

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::postRemove,
            Events::postPersist,
            Events::postFlush,
            Events::postUpdate,
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

    public function postUpdate(LifecycleEventArgs $args): void
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
            /** @var UserInterface $user */
            $topicsCount = $em->getRepository(ForumPost::class)->countByUser($user);
            $user->setForumPostsCount($topicsCount);
        }

        $this->users = [];

        $em->flush();
    }

    private function process(LifecycleEventArgs $args): void
    {
        $post = $args->getObject();

        if (
            !$post instanceof ForumPost
            || (null === $user = $post->getAuthor())
            || (null === $userId = $user->getId())
            || isset($this->users[$userId])
        ) {
            return;
        }

        $this->users[$userId] = $user;
    }
}
