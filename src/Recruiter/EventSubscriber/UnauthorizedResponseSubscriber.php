<?php

namespace App\Recruiter\EventSubscriber;

use App\Recruiter\Security\AccessTokenUtils;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class UnauthorizedResponseSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => 'onUnauthorizedResponse',
        ];
    }

    public function onUnauthorizedResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();

        if (Response::HTTP_UNAUTHORIZED === $response->getStatusCode()) {
            $response->headers->clearCookie(AccessTokenUtils::$cookieName);
        }
    }
}
