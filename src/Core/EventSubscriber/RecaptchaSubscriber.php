<?php

namespace App\Core\EventSubscriber;

use ReCaptcha\ReCaptcha;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class RecaptchaSubscriber implements EventSubscriberInterface
{
    private ReCaptcha $recaptchaClient;
    private bool $googleRecaptchaEnabled;

    public function __construct(string $googleRecaptchaSecret, bool $googleRecaptchaEnabled)
    {
        $this->recaptchaClient = new ReCaptcha($googleRecaptchaSecret);
        $this->googleRecaptchaEnabled = $googleRecaptchaEnabled;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (false === $event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        if (false === $this->googleRecaptchaEnabled) {
            return;
        }

        if ('api_contacts_freework_post_collection' !== $request->attributes->get('_route')) {
            return;
        }

        if (null === $recaptcha = $request->headers->get('X-Recaptcha')) {
            throw new HttpException(401, 'Recaptcha token is missing.');
        }

        $response = $this->recaptchaClient->setScoreThreshold(0.7)->verify($recaptcha);

        if (!$response->isSuccess()) {
            throw new HttpException(401, 'Recaptcha token is invalid.');
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 10],
        ];
    }
}
