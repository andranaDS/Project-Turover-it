<?php

namespace App\Recruiter\Controller\Turnover\Recruiter;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Recruiter\Entity\Recruiter;
use App\Recruiter\Manager\RecruiterManager;
use App\Recruiter\Model\ChangePasswordForm;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Utils\Json;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class ChangePassword
{
    public function __invoke(
        Request $request,
        Recruiter $recruiter,
        EntityManagerInterface $em,
        DenormalizerInterface $denormalizer,
        ValidatorInterface $validator,
        RecruiterManager $rm
    ): Recruiter {
        /** @var ChangePasswordForm $form */
        $form = $denormalizer->denormalize(Json::decode($request->getContent(), Json::FORCE_ARRAY), ChangePasswordForm::class);

        // build validation goups
        $groups = ['change_password:new_password'];
        if (null !== $form->getOldPassword() || false === $recruiter->isPasswordUpdateRequired()) {
            // old password is required
            $groups[] = 'change_password:old_password';
        }

        // validate
        $validator->validate($form, [
            'groups' => $groups,
        ]);

        $rm->setPassword($recruiter, $form->getNewPassword()); // @phpstan-ignore-line newPassword is a string because it is a mandatory property
        $recruiter->setPasswordUpdateRequired(false);

        $em->flush();

        return $recruiter;
    }
}
