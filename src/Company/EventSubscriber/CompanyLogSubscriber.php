<?php

namespace App\Company\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Company\Event\CompanyFeatureEvents;
use App\Recruiter\Entity\Recruiter;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;

class CompanyLogSubscriber implements EventSubscriberInterface
{
    private EventDispatcherInterface $dispatcher;
    private Security $security;

    public function __construct(EventDispatcherInterface $dispatcher, Security $security)
    {
        $this->dispatcher = $dispatcher;
        $this->security = $security;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [
                ['preRead', EventPriorities::POST_READ],
            ],
        ];
    }

    public function preRead(RequestEvent $event): void
    {
        $recruiter = $this->security->getUser();

        if ('api_companies_get_item' !== $event->getRequest()->attributes->get('_route') || !$recruiter instanceof Recruiter || null === $company = $recruiter->getCompany()) {
            return;
        }

        $this->dispatcher->dispatch(new GenericEvent($company), CompanyFeatureEvents::COMPANY_LOG);
    }
}
