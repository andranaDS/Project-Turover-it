<?php

namespace App\User\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\User\Entity\User;
use App\User\Enum\UserProfileStep;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class UserFormStepSubscriber implements EventSubscriberInterface
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
        $request = $event->getRequest();
        $route = $request->attributes->get('_route');
        $user = $event->getControllerResult();

        if (null === $user) {
            return;
        }
        /** @var User $user */
        if (1 === preg_match('/api_users_freework_patch_profile_(.*?)_item/', $route, $match) && false === $user->getProfileCompleted()) {
            $step = $match[1];
            if (UserProfileStep::isValidValue($step)) {
                $user->setFormStep($step);
            } else {
                throw new BadRequestException();
            }
        }
    }
}
