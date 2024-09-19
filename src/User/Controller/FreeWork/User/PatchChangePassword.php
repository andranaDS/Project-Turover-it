<?php

namespace App\User\Controller\FreeWork\User;

use App\User\Entity\User;
use App\User\Form\Model\ChangePasswordForm;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PatchChangePassword
{
    public function __invoke(
        Request $request,
        User $user,
        EntityManagerInterface $em,
        DenormalizerInterface $denormalizer,
        ValidatorInterface $validator,
        NormalizerInterface $normalizer): Response
    {
        try {
            $data = Json::decode($request->getContent(), Json::FORCE_ARRAY);
        } catch (JsonException $e) {
            return new JsonResponse(['hydra:title' => 'Json not valid'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $changePasswordForm = $denormalizer->denormalize($data, ChangePasswordForm::class);

        $violations = $validator->validate($changePasswordForm);

        if ($violations->count() > 0) {
            return new JsonResponse($normalizer->normalize($violations, 'jsonld'), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user->setPlainPassword($changePasswordForm->getNewPassword())
            ->setUpdatedAt(new \DateTime())
        ;

        $em->flush();

        return new JsonResponse($normalizer->normalize($user, 'jsonld', [
            'groups' => ['user:get', 'user:get:private'],
        ]), Response::HTTP_OK);
    }
}
