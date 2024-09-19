<?php

namespace App\Core\EventSubscriber;

use App\Core\Util\AcceptLanguageConverter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleSubscriber implements EventSubscriberInterface
{
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if (null === $acceptLanguage = AcceptLanguageConverter::extractAcceptLanguageFromRequest($request)) {
            return;
        }
        if (null === $locale = AcceptLanguageConverter::acceptLanguageToLocale($acceptLanguage)) {
            return;
        }
        $request->setLocale($locale);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 20],
        ];
    }
}
