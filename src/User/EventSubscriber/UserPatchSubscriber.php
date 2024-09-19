<?php

namespace App\User\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\User\Entity\User;
use App\User\Enum\UserProfileStep;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class UserPatchSubscriber implements EventSubscriberInterface
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
        $user = $event->getControllerResult();
        $route = $event->getRequest()->attributes->get('_route');

        if (
            !$user instanceof User ||
            1 !== preg_match('/api_users_freework_patch_profile_(.*?)_item/', $route, $match) ||
            false === UserProfileStep::isValidValue($match[1])
        ) {
            return;
        }

        $user->setUpdatedAt(new \DateTime());
    }
}
