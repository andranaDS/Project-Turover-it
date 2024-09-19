<?php

namespace App\Recruiter\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Core\Util\Strings;
use App\Recruiter\Entity\Recruiter;
use Carbon\Carbon;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RegistrationPrePersistSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['onPreValidate', EventPriorities::PRE_VALIDATE],
        ];
    }

    public function onPreValidate(ViewEvent $event): void
    {
        $recruiter = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$recruiter instanceof Recruiter || Request::METHOD_POST !== $method) {
            return;
        }

        // job format
        if (null !== $recruiter->getJob()) {
            $recruiter->setJob(Strings::jobCase($recruiter->getJob()));
        }

        // legacy compatibility
        $recruiter->setUsername($recruiter->getEmail());

        // tos datetime
        if (true === $recruiter->getTermsOfService()) {
            $recruiter->setTermsOfServiceAcceptedAt(Carbon::now());
        }
    }
}
