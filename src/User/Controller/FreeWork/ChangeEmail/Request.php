<?php

namespace App\User\Controller\FreeWork\ChangeEmail;

use App\Core\Mailer\Mailer;
use App\Core\Util\TokenGenerator;
use App\User\Email\ChangeEmailRequestEmail;
use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class Request
{
    /**
     * @Route(
     *     name="api_user_freework_change_email_request",
     *     path="/change_email/request",
     *     methods={"PATCH"},
     *     condition= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
     * )
     */
    public function __invoke(
        HttpFoundationRequest $request,
        EntityManagerInterface $em,
        Mailer $mailer,
        NormalizerInterface $normalizer,
        ValidatorInterface $validator,
        Security $security,
        int $emailRequestTtl,
        LoggerInterface $logger
    ): Response {
        if ((null === $loggedUser = $security->getUser()) || !$loggedUser instanceof User) {
            return new Response(status: Response::HTTP_FORBIDDEN);
        }

        try {
            $data = Json::decode($request->getContent(), Json::FORCE_ARRAY);
        } catch (JsonException $e) {
            return new JsonResponse(['hydra:title' => 'Json not valid'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $newEmail = $data['email'] ?? null;

        $violations = $validator->validate((new User())->setEmail($newEmail), null, ['user:change_email:request']);
        if ($violations->count() > 0) {
            return new JsonResponse($normalizer->normalize($violations, 'jsonld'), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $token = TokenGenerator::generateFromValue($newEmail, 20);
        $loggedUser
            ->setEmailRequestedAt(new \DateTime())
            ->setConfirmationToken($token)
        ;
        $em->flush();

        try {
            $email = (new ChangeEmailRequestEmail())
                ->to($newEmail)
                ->context([
                    'user' => $loggedUser,
                    'newEmail' => $newEmail,
                    'token' => $token,
                ])
            ;

            $mailer->send($email);
        } catch (\Exception $e) {
            $logger->error($e->getMessage());
        }

        return new Response();
    }
}
