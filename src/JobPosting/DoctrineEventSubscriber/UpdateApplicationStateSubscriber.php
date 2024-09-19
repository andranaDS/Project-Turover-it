<?php

namespace App\JobPosting\DoctrineEventSubscriber;

use App\JobPosting\Entity\Application;
use App\JobPosting\Enum\ApplicationState;
use App\JobPosting\Enum\ApplicationStep;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UpdateApplicationStateSubscriber implements EventSubscriber
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
        $application = $args->getObject();

        if (!$application instanceof Application) {
            return;
        }

        if ($args instanceof PreUpdateEventArgs && false === $args->hasChangedField('step')) {
            return;
        }

        $step = $application->getStep();

        if (ApplicationStep::KO === $step) {
            $state = ApplicationState::UNSUCCESSFUL;
        } elseif (ApplicationStep::CANCELLED === $step) {
            $state = ApplicationState::CANCELLED;
        } else {
            $jobPosting = $application->getJobPosting();
            if ($jobPosting) {
                $state = $jobPosting->getPublished() ? ApplicationState::IN_PROGRESS : ApplicationState::EXPIRED;
            } else {
                $state = ApplicationState::IN_PROGRESS;
            }
        }

        $application->setState($state);
    }
}
