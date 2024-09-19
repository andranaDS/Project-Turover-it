<?php

namespace App\Company\DoctrineEventSubscriber;

use App\Company\Entity\Company;
use App\Company\Manager\CompanyQualityManager;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UpdateCompanyQualityScoreSubscriber implements EventSubscriber
{
    /** @var Company[] */
    private array $companies = [];
    private CompanyQualityManager $cqm;

    public function __construct(CompanyQualityManager $cqm)
    {
        $this->cqm = $cqm;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postUpdate,
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

    public function postFlush(PostFlushEventArgs $args): void
    {
        if (empty($this->companies)) {
            return;
        }

        $em = $args->getObjectManager();

        foreach ($this->companies as $company) {
            $company->setQuality($this->cqm->getQuality($company));
        }

        $this->companies = [];

        $em->flush();
    }

    private function process(LifecycleEventArgs $args): void
    {
        $company = $args->getObject();

        if (!$company instanceof Company) {
            return;
        }

        $this->companies[$company->getId()] = $company;
    }
}
