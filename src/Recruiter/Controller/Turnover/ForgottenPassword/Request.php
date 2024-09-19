<?php

namespace App\Recruiter\Controller\Turnover\ForgottenPassword;

use App\Core\Mailer\Mailer;
use App\Core\Util\TokenGenerator;
use App\Recruiter\Email\ForgottenPasswordRequestEmail;
use App\Recruiter\Entity\Recruiter;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class Request
{
    /**
     * @Route(name="api_recruiter_turnover_forgotten_password_request", path="/forgotten_password/request", methods={"POST"}, host="%api_turnover_base_url%")
     */
    public function __invoke(
        HttpFoundationRequest $request,
        EntityManagerInterface $em,
        ValidatorInterface $validator,
        NormalizerInterface $normalizer,
        Mailer $mailer,
        RouterInterface $router,
        int $passwordRequestTtl
    ): Response {
        try {
            $data = Json::decode($request->getContent(), Json::FORCE_ARRAY);
        } catch (JsonException $e) {
            return new JsonResponse(['hydra:title' => 'Json not valid'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $email = $data['email'] ?? null;
        $recruiter = (new Recruiter())
            ->setEmail($email)
        ;

        $violations = $validator->validate($recruiter, null, ['recruiter:forgotten_password:reset']);
        if ($violations->count() > 0) {
            return new JsonResponse($normalizer->normalize($violations, 'jsonld'), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        /** @var Recruiter $recruiter */
        $recruiter = $em->getRepository(Recruiter::class)->findOneByEmail($email);

        if (null !== $recruiter && false === $recruiter->isPasswordRequestActive($passwordRequestTtl) && null !== $recruiter->getEmail()) {
            $recruiter
                ->setPasswordRequestedAt(new \DateTime())
                ->setConfirmationToken(TokenGenerator::generate())
            ;
            $em->flush();

            $email = (new ForgottenPasswordRequestEmail())
                ->to($recruiter->getEmail())
                ->setVariables([
                    'first_name' => $recruiter->getFirstName(),
                    'reset_link' => $router->generate('turnover_front_reset_password', [
                        'token' => $recruiter->getConfirmationToken(),
                    ], UrlGeneratorInterface::ABSOLUTE_URL),
                ])
            ;

            $mailer->sendRecruiter($email, $recruiter);
        }

        return new Response();
    }
}
