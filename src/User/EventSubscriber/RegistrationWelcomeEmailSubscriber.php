<?php

namespace App\User\EventSubscriber;

use App\Core\Mailer\Mailer;
use App\User\Email\RegistrationWelcomeEmail;
use App\User\Entity\User;
use App\User\Event\UserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class RegistrationWelcomeEmailSubscriber implements EventSubscriberInterface
{
    private Mailer $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserEvents::REGISTRATION_EMAIL_CONFIRMED => 'sendEmail',
            UserEvents::PROVIDER_USER_CREATED => 'sendEmail',
        ];
    }

    public function sendEmail(GenericEvent $event): void
    {
        $user = $event->getSubject();

        if (!$user instanceof User) {
            return;
        }

        if (null !== $user->getEmail()) {
            $email = (new RegistrationWelcomeEmail())
                ->to($user->getEmail())
                ->context([
                    'user' => $user,
                ])
            ;

            $this->mailer->send($email);
        }
    }
}
