<?php

namespace App\Recruiter\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Contracts\Translation\TranslatorInterface;

class AuthenticationFailureHandler implements AuthenticationFailureHandlerInterface
{
    private NormalizerInterface $normalizer;
    private TranslatorInterface $translator;

    public function __construct(NormalizerInterface $normalizer, TranslatorInterface $translator)
    {
        $this->normalizer = $normalizer;
        $this->translator = $translator;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        $message = $exception instanceof DisabledException ?
            $this->translator->trans('recruiter.authentication.error.disabled')
            : $this->translator->trans('recruiter.authentication.error.bad_credentials');

        return new JsonResponse($this->normalizer->normalize(new ConstraintViolationList([
            new ConstraintViolation('', null, [], null, 'email', null),
            new ConstraintViolation('', null, [], null, 'password', null),
        ]), 'jsonld', [
            'title' => $message,
        ]), Response::HTTP_UNAUTHORIZED);
    }
}
