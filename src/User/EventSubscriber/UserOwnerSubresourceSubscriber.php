<?php

namespace App\User\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\User\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;

class UserOwnerSubresourceSubscriber implements EventSubscriberInterface
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [
                ['process', EventPriorities::PRE_READ],
            ],
        ];
    }

    public function process(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!\in_array($request->attributes->get('_route'), [
            'api_users_applications_get_subresource',
            'api_users_documents_get_subresource',
            'api_users_job_posting_searches_get_subresource',
        ], true)) {
            return;
        }

        $user = $this->security->getUser();
        if ($user instanceof User && $user->getId() !== (int) $request->attributes->get('id')) {
            throw new AccessDeniedHttpException('Access Denied.');
        }
    }
}
