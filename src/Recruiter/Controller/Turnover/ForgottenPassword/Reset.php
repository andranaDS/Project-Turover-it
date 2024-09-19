<?php

namespace App\Recruiter\Controller\Turnover\ForgottenPassword;

use App\Core\Mailer\Mailer;
use App\Recruiter\Email\ForgottenPasswordResetEmail;
use App\Recruiter\Entity\Recruiter;
use App\Recruiter\Manager\RecruiterManager;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class Reset
{
    /**
     * @Route(name="api_recruiter_turnover_forgotten_password_reset", path="/forgotten_password/reset", methods={"POST"}, host="%api_turnover_base_url%")
     */
    public function __invoke(Request $request, EntityManagerInterface $em, ValidatorInterface $validator, NormalizerInterface $normalizer, TranslatorInterface $translator, RouterInterface $router, RecruiterManager $rm, Mailer $mailer, int $passwordRequestTtl): Response
    {
        try {
            $data = Json::decode($request->getContent(), Json::FORCE_ARRAY);
        } catch (JsonException $e) {
            return new JsonResponse(['hydra:title' => 'Json not valid'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $token = $data['token'] ?? null;
        $plainPassword = $data['plainPassword'] ?? null;

        // 1 fetch token
        if (null === $token) {
            return new JsonResponse(['hydra:title' => $translator->trans('recruiter.forgotten_password.reset.error.link_expired')], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // 2 fetch user
        /** @var ?Recruiter $recruiter */
        $recruiter = $em->getRepository(Recruiter::class)->findOneByConfirmationToken($token);
        if (null === $recruiter) {
            return new JsonResponse(['hydra:title' => $translator->trans('recruiter.forgotten_password.reset.error.link_expired')], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // 3 check token validity
        if (false === $recruiter->isPasswordRequestActive($passwordRequestTtl)) {
            $recruiter
                ->setPasswordRequestedAt(null)
                ->setConfirmationToken(null)
            ;
            $em->flush();

            return new JsonResponse(['hydra:title' => $translator->trans('recruiter.forgotten_password.reset.error.link_expired')], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // 4 check password complexity
        if (null !== $plainPassword && null !== $recruiter->getEmail()) {
            $recruiter->setPlainPassword($plainPassword);

            $violations = $validator->validate($recruiter, null, ['recruiter:forgotten_password:reset']);
            if ($violations->count() > 0) {
                return new JsonResponse($normalizer->normalize($violations, 'jsonld'), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $recruiter
                ->setPasswordRequestedAt(null)
                ->setConfirmationToken(null)
                ->setEnabled(true)
                ->setPasswordUpdateRequired(false)
            ;
            $rm->setPassword($recruiter, $plainPassword);

            $em->flush();

            if (!empty($recruiter->getEmail())) {
                $email = (new ForgottenPasswordResetEmail())
                    ->to($recruiter->getEmail())
                    ->setVariables([
                        'first_name' => $recruiter->getFirstName(),
                        'link' => $router->generate('turnover_front_home', [], UrlGeneratorInterface::ABSOLUTE_URL),
                    ])
                ;

                $mailer->sendRecruiter($email, $recruiter);
            }
        }

        return new JsonResponse($normalizer->normalize($recruiter, 'jsonld', [
            'groups' => ['recruiter:get'],
        ]), Response::HTTP_OK);
    }
}
