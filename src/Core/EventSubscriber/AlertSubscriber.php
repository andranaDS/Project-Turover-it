<?php

namespace App\Core\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Core\Entity\Alert;
use App\Core\Repository\AlertRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class AlertSubscriber implements EventSubscriberInterface
{
    private AlertRepository $repository;

    public function __construct(AlertRepository $repository)
    {
        $this->repository = $repository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => ['onKernelResponse', EventPriorities::POST_RESPOND],
        ];
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $alert = $this->repository->findOnGoingAlert();

        if ($alert instanceof Alert) {
            $event->getResponse()->headers->add(['X-Alert' => $alert->getId()]);
        }
    }
}
