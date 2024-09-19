<?php

namespace App\Company\DoctrineEventSubscriber;

use App\Company\Manager\CompanyManager;
use App\User\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UpdateCompanyUserIntercontractCountSubscriber implements EventSubscriber
{
    private array $companies = [];
    private CompanyManager $cm;

    public function __construct(CompanyManager $cm)
    {
        $this->cm = $cm;
    }

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
        if (empty($this->companies)) {
            return;
        }

        foreach ($this->companies as $company) {
            $this->cm->updateUsersCount($company);
        }

        $this->companies = [];
        $args->getObjectManager()->flush();
    }

    private function process(LifecycleEventArgs $args): void
    {
        $user = $args->getObject();

        if ((!$user instanceof User)
            || (null === $recruiter = $user->getCreatedBy())
            || (null === $company = $recruiter->getCompany())
        ) {
            return;
        }

        $this->companies[] = $company;
    }
}
