<?php

namespace App\User\EventSubscriber;

use App\User\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Contracts\Translation\TranslatorInterface;

class JWTAuthenticationFailureDisabledSubscriber implements EventSubscriberInterface
{
    private NormalizerInterface $normalizer;
    private TranslatorInterface $translator;

    public function __construct(NormalizerInterface $normalizer, TranslatorInterface $translator)
    {
        $this->normalizer = $normalizer;
        $this->translator = $translator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events::AUTHENTICATION_FAILURE => 'onAuthenticationFailureResponse',
        ];
    }

    public function onAuthenticationFailureResponse(AuthenticationFailureEvent $event): void
    {
        $exception = $event->getException();

        if (!$exception instanceof DisabledException) {
            return;
        }

        $user = new User();

        $violations = new ConstraintViolationList([
            new ConstraintViolation($this->translator->trans('user.authentication.error.disabled'), null, [], $user, 'email', null),
        ]);

        $event->setResponse(new JsonResponse($this->normalizer->normalize($violations, 'jsonld', [
            'title' => 'Disabled.',
        ]), Response::HTTP_UNAUTHORIZED));
    }
}
