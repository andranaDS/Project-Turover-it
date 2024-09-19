<?php

namespace App\Forum\DoctrineEventSubscriber;

use App\Forum\Entity\ForumPostUpvote;
use App\User\Contracts\UserInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UpdateUserUpvotesCountSubscriber implements EventSubscriber
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
            /** @var UserInterface $user */
            $upvotesCount = $em->getRepository(ForumPostUpvote::class)->countByUser($user);
            $user->setForumPostUpvotesCount($upvotesCount);
        }

        $this->users = [];

        $em->flush();
    }

    private function process(LifecycleEventArgs $args): void
    {
        $postUpvote = $args->getObject();

        if (
            !$postUpvote instanceof ForumPostUpvote
            || (null === $user = $postUpvote->getUser())
            || (null === $userId = $user->getId())
        ) {
            return;
        }

        $this->users[$userId] = $user;
    }
}
