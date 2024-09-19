<?php

namespace App\User\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\User\Entity\User;
use App\User\Enum\Availability;
use App\User\Manager\UserManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class UserPatchStatusSubscriber implements EventSubscriberInterface
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

        if (!$user instanceof User || 'api_users_freework_patch_status_item' !== $event->getRequest()->attributes->get('_route')) {
            return;
        }

        if (Availability::DATE !== $user->getAvailability()) {
            $user->setNextAvailabilityAt(UserManager::calculateNextAvailabilityAt($user->getAvailability()));
        }

        $user->setStatusUpdatedAt(new \DateTime());
    }
}
