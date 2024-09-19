<?php

namespace App\JobPosting\DoctrineEventSubscriber;

use App\Core\Entity\Job;
use App\Core\Util\JobDetector;
use App\JobPosting\Entity\JobPosting;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UpdateJobPostingJobSubscriber implements EventSubscriber
{
    private JobDetector $jobDetector;
    private static int $defaultJobId = 183;

    public function __construct(JobDetector $jobDetector)
    {
        $this->jobDetector = $jobDetector;
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
        $jobPosting = $args->getObject();

        if (!$jobPosting instanceof JobPosting) {
            return;
        }

        $job = null;

        if (!empty($jobPosting->getTitle())) {
            $job = $this->jobDetector->detect($jobPosting->getTitle());
        }

        if ((null === $job) && (null === $job = $args->getObjectManager()->find(Job::class, self::$defaultJobId))) {
            throw new \InvalidArgumentException('job is null');
        }

        $jobPosting->setJob($job);
    }
}
