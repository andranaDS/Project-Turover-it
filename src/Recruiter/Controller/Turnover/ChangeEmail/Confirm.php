<?php

namespace App\Recruiter\Controller\Turnover\ChangeEmail;

use App\Core\Util\TokenGenerator;
use App\Recruiter\Entity\Recruiter;
use App\Recruiter\Security\AccessTokenUtils;
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
     * @Route(name="api_recruiter_turnover_change_email_confirm", path="/change_email/confirm", methods={"PATCH"}, host="%api_turnover_base_url%")
     */
    public function __invoke(Request $request, EntityManagerInterface $em, ValidatorInterface $validator, NormalizerInterface $normalizer, TranslatorInterface $translator, AccessTokenUtils $atu, int $emailRequestTtl): Response
    {
        try {
            $data = Json::decode($request->getContent(), Json::FORCE_ARRAY);
        } catch (JsonException $e) {
            return new JsonResponse(['hydra:title' => 'Json not valid'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $token = $data['token'] ?? null;
        $email = $data['email'] ?? null;

        // 1 fetch token
        if (null === $token) {
            return new JsonResponse(['hydra:title' => $translator->trans('recruiter.change_email.confirm.error.link_expired')], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // 2 fetch recruiter
        /** @var ?Recruiter $recruiter */
        $recruiter = $em->getRepository(Recruiter::class)->findOneByConfirmationToken($token);
        if (null === $recruiter) {
            return new JsonResponse(['hydra:title' => $translator->trans('recruiter.change_email.confirm.error.link_expired')], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // 3 check token email validity
        if (TokenGenerator::generateFromValue($email, 20) !== $token) {
            return new JsonResponse(['hydra:title' => $translator->trans('recruiter.change_email.confirm.error.link_corrupted')], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // 4 check token ttl validity
        if (false === $recruiter->isEmailRequestActive($emailRequestTtl)) {
            $recruiter->setEmailRequestedAt(null)
                ->setConfirmationToken(null)
            ;
            $em->flush();

            return new JsonResponse(['hydra:title' => $translator->trans('recruiter.change_email.confirm.error.link_expired')], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // 5 check email uniqueness
        $violations = $validator->validate((new Recruiter())->setEmail($email), null, ['recruiter:change_email:request']);
        if ($violations->count() > 0) {
            return new JsonResponse($normalizer->normalize($violations, 'jsonld'), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // 6 update email
        $recruiter->setEmail($email)
            ->setEmailRequestedAt(null)
            ->setConfirmationToken(null)
        ;

        $em->flush();

        $response = new JsonResponse($normalizer->normalize($recruiter, 'jsonld', [
            'groups' => ['recruiter:get'],
        ]), Response::HTTP_OK);

        // 7 logout
        $atu->logout($recruiter, $response);

        return $response;
    }
}
