<?php

namespace App\JobPosting\DoctrineEventSubscriber;

use App\JobPosting\Entity\JobPosting;
use App\JobPosting\Enum\ApplicationType;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class CleanJobPostingSubscriber implements EventSubscriber
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

        if (ApplicationType::TURNOVER === $jobPosting->getApplicationType()) {
            $jobPosting->setApplicationContact(null)
                ->setApplicationUrl(null)
            ;
        } elseif (ApplicationType::CONTACT === $jobPosting->getApplicationType()) {
            $jobPosting->setApplicationEmail(null)
                ->setApplicationUrl(null)
            ;
        } elseif (ApplicationType::URL === $jobPosting->getApplicationType()) {
            $jobPosting->setApplicationEmail(null)
                ->setApplicationContact(null)
            ;
        }

        if (false === $jobPosting->hasFreeContract()) {
            $jobPosting->setMinDailySalary(null)
                ->setMaxDailySalary(null)
            ;
        }
        if (false === $jobPosting->hasWorkContract()) {
            $jobPosting->setMinAnnualSalary(null)
                ->setMaxAnnualSalary(null)
            ;
        }
        if (false === $jobPosting->hasTemporaryContract()) {
            $jobPosting->setDurationValue(null)
                ->setDurationPeriod(null)
            ;
        }
    }
}
