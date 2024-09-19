<?php

namespace App\Resource\DoctrineEventSubscriber;

use App\JobPosting\Enum\Contract;
use App\Resource\Entity\Contribution;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class CreateContributionSubscriber implements EventSubscriber
{
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $contribution = $args->getObject();

        if (!$contribution instanceof Contribution) {
            return;
        }

        if (Contract::isFree($contribution->getContract())) {
            $contribution->setAnnualSalary(null);
            $contribution->setVariableAnnualSalary(null);
        }

        if (Contract::isWork($contribution->getContract())) {
            $contribution->setUserCompanyStatus(null);
            $contribution->setDailySalary(null);
        }
    }
}
