<?php

namespace App\User\EventSubscriber;

use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationSuccessResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class UserLoginSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public static function getSubscribedEvents()
    {
        return [
            LoginSuccessEvent::class => 'process',
        ];
    }

    public function process(LoginSuccessEvent $event): void
    {
        $route = $event->getRequest()->get('_route');
        $response = $event->getResponse();

        if ('api_user_freework_security_login_provider' !== $route && 'api_user_freework_security_login' !== $route) {
            return;
        }

        if (false === $response instanceof JWTAuthenticationSuccessResponse) {
            return;
        }

        /** @var User $user */
        $user = $event->getUser();
        $provider = 'email';

        if ('api_user_freework_security_login_provider' === $route) {
            $provider = $event->getRequest()->attributes->get('provider');
        }

        $user->setLastLoginAt(new \DateTime())
            ->setLastLoginProvider($provider)
        ;

        $this->em->flush();
    }
}
