<?php

namespace App\Core\EventSubscriber;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;
use ApiPlatform\Core\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class PageNotFoundSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['onKernelView', EventPriorities::PRE_WRITE],
        ];
    }

    public function onKernelView(ViewEvent $event): void
    {
        $paginator = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$paginator instanceof Paginator || Request::METHOD_GET !== $method) {
            return;
        }

        $page = (int) $event->getRequest()->query->get('page', '1');

        if ($page > 1 && 0 === $paginator->count()) {
            throw new NotFoundHttpException('Page have no results');
        }
    }
}
