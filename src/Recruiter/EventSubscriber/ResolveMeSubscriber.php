<?php

namespace App\Recruiter\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Recruiter\Entity\Recruiter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;

class ResolveMeSubscriber implements EventSubscriberInterface
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['onPreRead', EventPriorities::PRE_READ],
        ];
    }

    public function onPreRead(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!\in_array($request->attributes->get('_route'), [
            'api_recruiters_turnover_get_item',
            'api_recruiters_turnover_patch_item',
            'api_recruiters_turnover_patch_webinar_item',
            'api_recruiters_turnover_delete_item',
            'api_recruiters_turnover_patch_change_password_item',
        ], true)) {
            return;
        }

        if ('me' !== $request->attributes->get('id')) {
            return;
        }

        $user = $this->security->getUser();
        if (!$user instanceof Recruiter || null === $user->getId()) {
            throw new NotFoundHttpException();
        }

        $request->attributes->set('id', $user->getId());
    }
}
