<?php

namespace App\Company\DoctrineEventSubscriber;

use App\Company\Entity\Company;
use App\JobPosting\Contracts\JobPostingInterface;
use App\JobPosting\Entity\JobPosting;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UpdateCompanyLastJobPostingDateSubscriber implements EventSubscriber
{
    private array $companies = [];

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postRemove,
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
        if (empty($this->companies)) {
            return;
        }

        $em = $args->getObjectManager();

        foreach ($this->companies as $company) {
            /* @var Company $company */
            /* @var JobPosting $jobPosting */
            if (null === $jobPosting = $em->getRepository(JobPosting::class)->findLastJobPostingByCompany($company)) {
                continue;
            }

            if (null !== $companyData = $company->getData()) {
                $companyData->setLastJobPostingDate($jobPosting->getPublishedAt());
            }
        }

        $this->companies = [];

        $em->flush();
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
