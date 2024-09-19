<?php

namespace App\JobPosting\DoctrineEventSubscriber;

use App\JobPosting\Entity\Application;
use App\JobPosting\Entity\JobPostingUserFavorite;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UpdateApplicationFavoriteAtSubscriber implements EventSubscriber
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
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
        $this->process($args);
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $this->process($args);
    }

    private function process(LifecycleEventArgs $args): void
    {
        $application = $args->getObject();

        if (!$application instanceof Application) {
            return;
        }

        if (null === $jobPosting = $application->getJobPosting()) {
            return;
        }

        $jobPostingFavorite = $this->em->getRepository(JobPostingUserFavorite::class)->findOneBy([
            'jobPosting' => $jobPosting,
            'user' => $application->getUser(),
        ]);

        if (null === $jobPostingFavorite) {
            return;
        }

        $application->setFavoriteAt($jobPostingFavorite->getCreatedAt());
    }
}
