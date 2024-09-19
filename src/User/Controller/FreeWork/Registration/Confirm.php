<?php

namespace App\User\Controller\FreeWork\Registration;

use App\User\Entity\User;
use App\User\Event\UserEvents;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
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
     * @Route(
     *     name="api_user_freework_registration_confirm",
     *     path="/registration/confirm", methods={"POST"},
     *     condition= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
     * )
     */
    public function __invoke(
        Request $request,
        EntityManagerInterface $em,
        NormalizerInterface $normalizer,
        TranslatorInterface $translator,
        EventDispatcherInterface $dispatcher,
        AuthenticationSuccessHandler $authenticationSuccessHandler,
        int $emailConfirmTtl
    ): Response {
        try {
            $data = Json::decode($request->getContent(), Json::FORCE_ARRAY);
        } catch (JsonException $e) {
            return new JsonResponse(['hydra:title' => 'Json not valid'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $token = $data['token'] ?? null;

        // 1 fetch token
        if (null === $token) {
            return new JsonResponse(['hydra:title' => $translator->trans('user.registration.confirm.error.link_expired')], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // 2 fetch user
        /** @var ?User $user */
        $user = $em->getRepository(User::class)->findOneByConfirmationToken($token);
        if (null === $user) {
            return new JsonResponse(['hydra:title' => $translator->trans('user.registration.confirm.error.link_expired')], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // 3 check token validity
        if (false === $user->isEmailConfirmActive($emailConfirmTtl)) {
            // hard delete if a user try to confirm this email after the deadline
            $em->getFilters()->enable('soft_deleteable');
            $em->remove($user);
            $em->flush();

            return new JsonResponse(['hydra:title' => $translator->trans('user.registration.confirm.error.link_expired')], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // 4 enable user
        $user
            ->setEnabled(true)
            ->setConfirmationToken(null)
        ;

        $em->flush();

        $response = new JsonResponse($normalizer->normalize($user, 'jsonld', [
            'groups' => ['user:get'],
        ]), Response::HTTP_OK);

        $dispatcher->dispatch(new GenericEvent($user, [
            'response' => $response,
        ]), UserEvents::REGISTRATION_EMAIL_CONFIRMED);

        return $response;
    }
}
