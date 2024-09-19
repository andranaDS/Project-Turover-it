<?php

namespace App\User\Controller\FreeWork\ForgottenPassword;

use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class Reset
{
    /**
     * @Route(
     *     name="api_user_freework_forgotten_password_reset",
     *     path="/forgotten_password/reset",
     *     methods={"POST"},
     *     condition= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
     * )
     */
    public function __invoke(
        Request $request,
        EntityManagerInterface $em,
        ValidatorInterface $validator,
        NormalizerInterface $normalizer,
        TranslatorInterface $translator,
        int $passwordRequestTtl
    ): Response {
        try {
            $data = Json::decode($request->getContent(), Json::FORCE_ARRAY);
        } catch (JsonException $e) {
            return new JsonResponse(['hydra:title' => 'Json not valid'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $token = $data['token'] ?? null;
        $plainPassword = $data['plainPassword'] ?? null;

        // 1 fetch token
        if (null === $token) {
            return new JsonResponse(['hydra:title' => $translator->trans('user.forgotten_password.reset.error.link_expired')], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // 2 fetch user
        /** @var ?User $user */
        $user = $em->getRepository(User::class)->findOneByConfirmationToken($token);
        if (null === $user) {
            return new JsonResponse(['hydra:title' => $translator->trans('user.forgotten_password.reset.error.link_expired')], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // 3 check token validity
        if (false === $user->isPasswordRequestActive($passwordRequestTtl)) {
            $user->setPasswordRequestedAt(null)
                ->setConfirmationToken(null)
            ;
            $em->flush();

            return new JsonResponse(['hydra:title' => $translator->trans('user.forgotten_password.reset.error.link_expired')], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // 4 check password complexity
        if (null !== $plainPassword) {
            $user->setPlainPassword($plainPassword)
                ->setPasswordRequestedAt(null)
                ->setConfirmationToken(null)
                ->setEnabled(true)
            ;

            $violations = $validator->validate($user, null, ['user:forgotten_password:reset']);
            if ($violations->count() > 0) {
                return new JsonResponse($normalizer->normalize($violations, 'jsonld'), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $em->flush();
        }

        return new JsonResponse($normalizer->normalize($user, 'jsonld', [
            'groups' => ['user:get', 'user:get:private'],
        ]), Response::HTTP_OK);
    }
}
