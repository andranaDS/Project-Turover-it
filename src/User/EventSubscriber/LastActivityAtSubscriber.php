<?php

namespace App\User\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\User\Entity\User;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Utils\Strings;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;

class LastActivityAtSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', EventPriorities::PRE_READ],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $user = $this->security->getUser();
        $request = $event->getRequest();

        if (!$user instanceof User) {
            return;
        }

        if ('GET' === $event->getRequest()->getMethod()) {
            return;
        }

        if (null === $data = $user->getData()) {
            return;
        }

        $data->setLastActivityAt(Carbon::now());

        $routeName = $request->attributes->get('_route');
        if (null !== $routeName && true === Strings::startsWith($routeName, 'api_forum_')) {
            $data->setLastForumActivityAt(Carbon::now());
        }

        $this->em->flush();
    }
}
