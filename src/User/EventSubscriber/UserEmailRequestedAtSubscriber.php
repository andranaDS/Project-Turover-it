<?php

namespace App\User\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class UserEmailRequestedAtSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $em;
    private int $passwordRequestTtl;

    public function __construct(EntityManagerInterface $em, int $passwordRequestTtl)
    {
        $this->em = $em;
        $this->passwordRequestTtl = $passwordRequestTtl;
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

        if ('api_users_get_item' !== $request->attributes->get('_route')) {
            return;
        }

        $userId = $request->attributes->get('id');
        $user = $this->em->getRepository(User::class)->findOneById($userId);

        if (null !== $user && $user->getEmailRequestedAt()) {
            if (false === $user->isEmailRequestActive($this->passwordRequestTtl)) {
                $user->setEmailRequestedAt(null)
                    ->setConfirmationToken(null)
                ;
                $this->em->flush();
            }
        }
    }
}
