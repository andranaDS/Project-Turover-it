<?php

namespace App\Company\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Company\Entity\Company;
use App\Company\Event\CompanyFeatureEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class CompanyPublishSubscriber implements EventSubscriberInterface
{
    private EventDispatcherInterface $dispatcher;
    private ?bool $isDirectoryTurnover = null;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [
                ['preDeserialize', EventPriorities::PRE_DESERIALIZE],
            ],
            KernelEvents::VIEW => [
                ['preWrite', EventPriorities::PRE_WRITE],
            ],
        ];
    }

    public function preWrite(ViewEvent $event): void
    {
        $company = $event->getControllerResult();

        if ('api_companies_turnover_patch_directory_item' !== $event->getRequest()->attributes->get('_route') || !$company instanceof Company) {
            return;
        }

        if (false === $this->isDirectoryTurnover && true === $company->isDirectoryTurnover()) {
            $this->dispatcher->dispatch(new GenericEvent($company), CompanyFeatureEvents::COMPANY_PUBLISH);
        }

        $this->isDirectoryTurnover = null;
    }

    public function preDeserialize(RequestEvent $event): void
    {
        $company = $event->getRequest()->attributes->get('data');

        if ('api_companies_turnover_patch_directory_item' !== $event->getRequest()->attributes->get('_route') || !$company instanceof Company) {
            return;
        }

        $this->isDirectoryTurnover = $company->isDirectoryTurnover();
    }
}
