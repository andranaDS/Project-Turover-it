<?php

namespace App\User\DoctrineEventSubscriber;

use App\JobPosting\Entity\JobPostingSearch;
use App\User\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UpdateActiveJobPostingSearchesCountSubscriber implements EventSubscriber
{
    /** @var User[] */
    private array $users = [];

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postUpdate,
            Events::postRemove,
            Events::postFlush,
        ];
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->process($args);
    }

    public function postUpdate(LifecycleEventArgs $args): void
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
            $user->setActiveJobPostingSearchesCount($em->getRepository(JobPostingSearch::class)->count([
                'user' => $user,
                'activeAlert' => true,
            ]));
        }

        $this->users = [];

        $em->flush();
    }

    private function process(LifecycleEventArgs $args): void
    {
        $jobPostingSearch = $args->getObject();

        if (!$jobPostingSearch instanceof JobPostingSearch) {
            return;
        }

        $user = $jobPostingSearch->getUser();

        if (null === $user->getId()) {
            return;
        }

        $this->users[$user->getId()] = $user;
    }
}
