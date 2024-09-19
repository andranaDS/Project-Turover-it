<?php

namespace App\User\Controller\FreeWork\ForgottenPassword;

use App\Core\Mailer\Mailer;
use App\Core\Util\TokenGenerator;
use App\User\Email\ForgottenPasswordResetEmail;
use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class Request
{
    /**
     * @Route(
     *     name="api_user_freework_forgotten_password_request",
     *     path="/forgotten_password/request",
     *     methods={"POST"},
     *     condition= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
     * )
     */
    public function __invoke(
        HttpFoundationRequest $request,
        EntityManagerInterface $em,
        ValidatorInterface $validator,
        NormalizerInterface $normalizer,
        Mailer $mailer,
        int $passwordRequestTtl,
        LoggerInterface $logger
    ): Response {
        try {
            $data = Json::decode($request->getContent(), Json::FORCE_ARRAY);
        } catch (JsonException $e) {
            return new JsonResponse(['hydra:title' => 'Json not valid'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $email = $data['email'] ?? null;
        $user = (new User())
            ->setEmail($email)
        ;

        $violations = $validator->validate($user, null, ['user:forgotten_password:request']);
        if ($violations->count() > 0) {
            return new JsonResponse($normalizer->normalize($violations, 'jsonld'), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        /** @var User $user */
        $user = $em->getRepository(User::class)->findOneByEmail($email);

        if (null !== $user && false === $user->isPasswordRequestActive($passwordRequestTtl)) {
            $user->setPasswordRequestedAt(new \DateTime())
                ->setConfirmationToken(TokenGenerator::generate())
            ;
            $em->flush();

            try {
                if (null !== $user->getEmail()) {
                    $email = (new ForgottenPasswordResetEmail())
                        ->to($user->getEmail())
                        ->context([
                            'user' => $user,
                        ])
                    ;
                }

                $mailer->send($email);
            } catch (\Exception $e) {
                $logger->error($e->getMessage());
            }
        }

        return new Response();
    }
}
