<?php

namespace App\JobPosting\DoctrineEventSubscriber;

use App\JobPosting\Entity\JobPosting;
use App\JobPosting\Enum\DurationPeriod;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UpdateJobPostingDurationSubscriber implements EventSubscriber
{
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

        $jobPosting->setDuration(match ($jobPosting->getDurationPeriod()) {
            DurationPeriod::DAY => (int) ceil($jobPosting->getDurationValue() / 30),
            DurationPeriod::MONTH => $jobPosting->getDurationValue(),
            DurationPeriod::YEAR => $jobPosting->getDurationValue() * 12,
            default => null
        });
    }
}
