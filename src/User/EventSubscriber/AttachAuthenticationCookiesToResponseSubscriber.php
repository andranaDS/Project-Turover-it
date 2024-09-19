<?php

namespace App\User\EventSubscriber;

use App\User\Entity\User;
use App\User\Event\UserEvents;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Response;

class AttachAuthenticationCookiesToResponseSubscriber implements EventSubscriberInterface
{
    private AuthenticationSuccessHandler $authenticationSuccessHandler;

    public function __construct(AuthenticationSuccessHandler $authenticationSuccessHandler)
    {
        $this->authenticationSuccessHandler = $authenticationSuccessHandler;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserEvents::REGISTRATION_EMAIL_CONFIRMED => 'addAuthenticationCookiesToResponseSubscriber',
        ];
    }

    public function addAuthenticationCookiesToResponseSubscriber(GenericEvent $event): void
    {
        $user = $event->getSubject();
        if (!$user instanceof User) {
            return;
        }

        $response = true === $event->hasArgument('response') ? $event->getArgument('response') : new Response();
        foreach ($this->authenticationSuccessHandler->handleAuthenticationSuccess($user)->headers->getCookies() as $cookie) {
            $response->headers->setCookie($cookie);
        }
    }
}
