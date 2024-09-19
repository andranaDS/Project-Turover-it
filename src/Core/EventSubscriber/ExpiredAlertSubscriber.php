<?php

namespace App\Core\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Core\Entity\Alert;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ExpiredAlertSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => ['onKernelResponse', EventPriorities::POST_RESPOND],
        ];
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if ('api_alerts_freework_get_item' !== $event->getRequest()->attributes->get('_route')) {
            return;
        }

        $alert = $event->getRequest()->attributes->get('data');

        if (!$alert instanceof Alert) {
            return;
        }

        if ($alert->isExpired()) {
            $event->getResponse()->setStatusCode(Response::HTTP_GONE);
        }
    }
}
