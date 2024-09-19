<?php

namespace App\User\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\User\Entity\User;
use App\User\Enum\UserProfileStep;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserProfileCompletedSubscriber implements EventSubscriberInterface
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

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

        if ('api_users_freework_patch_status_item' !== $request->attributes->get('_route')) {
            return;
        }

        /** @var User $user */
        $user = $event->getControllerResult();
        $formSteps = UserProfileStep::getMandatoriesSteps();

        $errors = [];
        foreach ($formSteps as $formStep) {
            $error = $this->validator->validate($user, null, ['user:patch:' . $formStep]);
            if ($error->count() > 0) {
                $errors[] = $error;
            }
        }

        if (0 === \count($errors)) {
            $user->setProfileCompleted(true);
        }
    }
}
