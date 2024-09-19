<?php

namespace App\User\Controller\FreeWork\ChangeEmail;

use App\Core\Util\TokenGenerator;
use App\User\Entity\User;
use App\User\Manager\UserManager;
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

final class Confirm
{
    /**
     * @Route(
     *     name="api_user_freework_change_email_confirm",
     *     path="/change_email/confirm",
     *     methods={"PATCH"},
     *     condition= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
     * )
     */
    public function __invoke(
        Request $request,
        EntityManagerInterface $em,
        ValidatorInterface $validator,
        NormalizerInterface $normalizer,
        TranslatorInterface $translator,
        UserManager $um,
        int $emailRequestTtl
    ): Response {
        try {
            $data = Json::decode($request->getContent(), Json::FORCE_ARRAY);
        } catch (JsonException $e) {
            return new JsonResponse(['hydra:title' => 'Json not valid'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $token = $data['token'] ?? null;
        $email = $data['email'] ?? null;

        // 1 fetch token
        if (null === $token) {
            return new JsonResponse(['hydra:title' => $translator->trans('user.change_email.confirm.error.link_expired')], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // 2 fetch user
        /** @var ?User $user */
        $user = $em->getRepository(User::class)->findOneByConfirmationToken($token);
        if (null === $user) {
            return new JsonResponse(['hydra:title' => $translator->trans('user.change_email.confirm.error.link_expired')], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // 3 check token email validity
        if (TokenGenerator::generateFromValue($email, 20) !== $token) {
            return new JsonResponse(['hydra:title' => $translator->trans('user.change_email.confirm.error.link_corrupted')], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // 4 check token ttl validity
        if (false === $user->isEmailRequestActive($emailRequestTtl)) {
            $user->setEmailRequestedAt(null)
                ->setConfirmationToken(null)
            ;
            $em->flush();

            return new JsonResponse(['hydra:title' => $translator->trans('user.change_email.confirm.error.link_expired')], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // 5 check email uniqueness
        $violations = $validator->validate((new User())->setEmail($email), null, ['user:change_email:request']);
        if ($violations->count() > 0) {
            return new JsonResponse($normalizer->normalize($violations, 'jsonld'), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // 6 update email
        $user->setEmail($email)
            ->setEmailRequestedAt(null)
            ->setConfirmationToken(null)
        ;

        $em->flush();

        $response = new JsonResponse($normalizer->normalize($user, 'jsonld', [
            'groups' => ['user:get'],
        ]), Response::HTTP_OK);

        // 7 logout
        $um->logout($response);

        return $response;
    }
}
