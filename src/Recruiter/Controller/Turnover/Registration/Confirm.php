<?php

namespace App\Recruiter\Controller\Turnover\Registration;

use App\Recruiter\Entity\Recruiter;
use App\Recruiter\Event\RecruiterEvents;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class Confirm
{
    /**
     * @Route(name="api_recruiter_turnover_registration_confirm", path="/registration/confirm", methods={"POST"}, host="%api_turnover_base_url%")
     */
    public function __invoke(Request $request, EntityManagerInterface $em, NormalizerInterface $normalizer, TranslatorInterface $translator, EventDispatcherInterface $dispatcher, int $emailConfirmTtl): Response
    {
        try {
            $data = Json::decode($request->getContent(), Json::FORCE_ARRAY);
        } catch (JsonException $e) {
            return new JsonResponse(['hydra:title' => 'Json not valid'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $token = $data['token'] ?? null;

        // 1 fetch token
        if (null === $token) {
            return new JsonResponse(['hydra:title' => $translator->trans('recruiter.registration.confirm.error.link_expired')], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // 2 fetch recruiter
        /** @var ?Recruiter $recruiter */
        $recruiter = $em->getRepository(Recruiter::class)->findOneByConfirmationToken($token);
        if (null === $recruiter) {
            return new JsonResponse(['hydra:title' => $translator->trans('recruiter.registration.confirm.error.link_expired')], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // 3 check token validity
        if (false === $recruiter->isEmailConfirmActive($emailConfirmTtl)) {
            return new JsonResponse(['hydra:title' => $translator->trans('recruiter.registration.confirm.error.link_expired')], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // 4 enable recruiter
        $recruiter
            ->setEnabled(true)
            ->setConfirmationToken(null)
        ;

        $em->flush();

        $response = new JsonResponse($normalizer->normalize($recruiter, 'jsonld', [
            'groups' => ['recruiter:get'],
        ]), Response::HTTP_OK);

        $dispatcher->dispatch(new GenericEvent($recruiter, [
            'response' => $response,
        ]), RecruiterEvents::REGISTRATION_EMAIL_CONFIRMED);

        return $response;
    }
}
