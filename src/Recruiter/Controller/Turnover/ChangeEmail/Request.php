<?php

namespace App\Recruiter\Controller\Turnover\ChangeEmail;

use App\Core\Mailer\Mailer;
use App\Core\Util\TokenGenerator;
use App\Recruiter\Email\ChangeEmailRequestEmail;
use App\Recruiter\Entity\Recruiter;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class Request
{
    /**
     * @Route(name="api_recruiter_turnover_change_email_request", path="/change_email/request", methods={"PATCH"}, host="%api_turnover_base_url%")
     * @IsGranted("ROLE_RECRUITER")
     */
    public function __invoke(HttpFoundationRequest $request, EntityManagerInterface $em, Mailer $mailer, NormalizerInterface $normalizer, ValidatorInterface $validator, ?UserInterface $recruiter, int $emailRequestTtl, RouterInterface $router): Response
    {
        if (!$recruiter instanceof Recruiter) {
            return new Response(status: Response::HTTP_BAD_REQUEST);
        }

        try {
            $data = Json::decode($request->getContent(), Json::FORCE_ARRAY);
        } catch (JsonException $e) {
            return new JsonResponse(['hydra:title' => 'Json not valid'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $newEmail = $data['email'] ?? null;

        $violations = $validator->validate((new Recruiter())->setEmail($newEmail), null, ['recruiter:change_email:request']);
        if ($violations->count() > 0) {
            return new JsonResponse($normalizer->normalize($violations, 'jsonld'), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $token = TokenGenerator::generateFromValue($newEmail, 20);
        $recruiter->setEmailRequestedAt(new \DateTime())
            ->setConfirmationToken($token)
        ;
        $em->flush();

        $email = (new ChangeEmailRequestEmail())
            ->to($newEmail)
            ->setVariables([
                'first_name' => $recruiter->getFirstName(),
                'confirmation_link' => $router->generate('turnover_front_change_email_confirm', [
                    'token' => $recruiter->getConfirmationToken(),
                    'email' => $newEmail,
                ], UrlGeneratorInterface::ABSOLUTE_URL),
            ])
        ;
        $mailer->send($email);

        return new Response();
    }
}
