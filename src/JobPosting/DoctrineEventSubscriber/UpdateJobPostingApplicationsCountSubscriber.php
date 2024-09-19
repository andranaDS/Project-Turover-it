<?php

namespace App\JobPosting\DoctrineEventSubscriber;

use App\JobPosting\Entity\Application;
use App\JobPosting\Entity\JobPosting;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UpdateJobPostingApplicationsCountSubscriber implements EventSubscriber
{
    private array $jobPostings = [];

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
        if (empty($this->jobPostings)) {
            return;
        }

        $em = $args->getObjectManager();

        foreach ($this->jobPostings as $jobPosting) {
            /** @var JobPosting $jobPosting */
            $applicationsCount = $em->getRepository(Application::class)->countByJobPosting($jobPosting);
            $jobPosting->setApplicationsCount($applicationsCount);
        }

        $this->jobPostings = [];

        $em->flush();
    }

    private function process(LifecycleEventArgs $args): void
    {
        $application = $args->getObject();

        if (
            !$application instanceof Application
            || (null === $jobPosting = $application->getJobPosting())
        ) {
            return;
        }

        $this->jobPostings[$jobPosting->getId()] = $jobPosting;
    }
}
