<?php

namespace App\Company\DoctrineEventSubscriber;

use App\Company\Manager\CompanyManager;
use App\JobPosting\Contracts\JobPostingInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UpdateCompanyJobPostingsCountSubscriber implements EventSubscriber
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
            $this->cm->updateJobPostingCounts($company);
        }

        $this->companies = [];
        $args->getObjectManager()->flush();
    }

    private function process(LifecycleEventArgs $args): void
    {
        $jobPosting = $args->getObject();

        if ((!$jobPosting instanceof JobPostingInterface) || (null === $company = $jobPosting->getCompany())) {
            return;
        }

        $this->companies[] = $company;
    }
}
