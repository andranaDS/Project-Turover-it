<?php

namespace App\Recruiter\EventSubscriber;

use App\Recruiter\Entity\Recruiter;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class InteractiveLoginSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public static function getSubscribedEvents()
    {
        return [
            InteractiveLoginEvent::class => 'onInteractiveLogin',
        ];
    }

    public function onInteractiveLogin(InteractiveLoginEvent $event): void
    {
        $recruiter = $event->getAuthenticationToken()->getUser();
        if (!$recruiter instanceof Recruiter) {
            return;
        }

        // enable the account when the recruiter secondary logs in for the first time
        if (null === $recruiter->getLoggedAt() && true === $recruiter->isSecondary()) {
            $recruiter->setEnabled(true);
        }

        // update the last log in date
        $recruiter->setLoggedAt(Carbon::now());

        $this->em->flush();
    }
}
