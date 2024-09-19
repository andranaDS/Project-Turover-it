<?php

namespace App\Recruiter\EventSubscriber;

use App\Core\Mailer\Mailer;
use App\Recruiter\Email\RegistrationEmailConfirmedEmail;
use App\Recruiter\Entity\Recruiter;
use App\Recruiter\Event\RecruiterEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class RegistrationMainEmailConfirmedSubscriber implements EventSubscriberInterface
{
    private Mailer $mailer;
    private RouterInterface $router;

    public function __construct(Mailer $mailer, RouterInterface $router)
    {
        $this->mailer = $mailer;
        $this->router = $router;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RecruiterEvents::REGISTRATION_EMAIL_CONFIRMED => 'onRegistrationEmailConfirmed',
        ];
    }

    public function onRegistrationEmailConfirmed(GenericEvent $event): void
    {
        $recruiter = $event->getSubject();

        if (!$recruiter instanceof Recruiter) {
            return;
        }

        $email = (new RegistrationEmailConfirmedEmail())
            ->setVariables([
                'first_name' => $recruiter->getFirstName(),
                'link' => $this->router->generate('turnover_front_login', referenceType: UrlGeneratorInterface::ABSOLUTE_URL),
            ])
        ;

        $this->mailer->sendRecruiter($email, $recruiter);
    }
}
