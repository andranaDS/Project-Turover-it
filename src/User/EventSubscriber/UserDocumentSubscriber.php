<?php

namespace App\User\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\User\Entity\UserDocument;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class UserDocumentSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => [
                ['process', EventPriorities::PRE_WRITE],
            ],
        ];
    }

    public function process(ViewEvent $event): void
    {
        $document = $event->getControllerResult();

        $route = $event->getRequest()->attributes->get('_route');
        $allowedRoutes = [
            'api_user_documents_post_document_collection',
            'api_user_documents_delete_item',
            'api_user_documents_put_item',
        ];

        if (!$document instanceof UserDocument || !\in_array($route, $allowedRoutes, true) || (null === $user = $document->getUser())) {
            return;
        }

        $user->setUpdatedAt(new \DateTime());
    }
}
