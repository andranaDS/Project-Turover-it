<?php

namespace App\User\Controller\FreeWork\User;

use ApiPlatform\Core\Validator\ValidatorInterface as ValidatorInterfaceApiPlatform;
use App\User\Contracts\ResumeParserInterface;
use App\User\Entity\User;
use App\User\Entity\UserDocument;
use App\User\Enum\UserProfileStep;
use Doctrine\ORM\EntityManagerInterface;
use ForceUTF8\Encoding;
use Nette\Utils\Arrays;
use Smalot\PdfParser\Parser;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class PostDocument
{
    public function __invoke(
        Request $request,
        ResumeParserInterface $resumeParser,
        ValidatorInterfaceApiPlatform $validatorApiPlatform,
        ValidatorInterface $validator,
        Security $security,
        EntityManagerInterface $em
    ): User {
        /** @var User $user */
        $user = $security->getUser();

        $uploadedFile = $request->files->get('documentFile');

        if (!$uploadedFile instanceof UploadedFile || (false === $uploadedFilePath = $uploadedFile->getRealPath())) {
            throw new BadRequestHttpException('"file" is required');
        }

        $resume = ('true' === $request->get('resume') || '1' === $request->get('resume'));
        $defaultResume = 0 === $user->getDefaultResumeDocuments()->count() && $resume;

        /** @var UploadedFile $uploadedFile */
        $userDocument = (new UserDocument())
            ->setOriginalName($uploadedFile->getClientOriginalName())
            ->setDocumentFile($uploadedFile)
            ->setDocument($request->get('document'))
            ->setResume($resume)
            ->setDefaultResume($defaultResume)
            ->setUser($user)
        ;

        $validatorApiPlatform->validate($userDocument, ['groups' => ['Default', 'user:post:document']]);

        if ($resume) {
            $originalUser = clone $user;

            if (false === $user->getProfileCompleted()) {
                $user->setFormStep(UserProfileStep::UPLOAD_RESUME);
                if (null === $resumeParser->parseResume($uploadedFilePath, $user, $userDocument) || null === $userDocument->getContent()) {
                    $this->updateUserDocumentContent($uploadedFilePath, $user, $userDocument);
                }
            } else {
                $this->updateUserDocumentContent($uploadedFilePath, $user, $userDocument);
            }

            // Try to validate all the profile
            $groups = Arrays::map(UserProfileStep::getMandatoriesSteps(), static function (string $step) {
                return sprintf('user:patch:%s', $step);
            });

            $violations = $validator->validate($originalUser, null, $groups);

            if (0 === \count($violations)) {
                $user->setProfileCompleted(true);
            }
        }

        $em->persist($userDocument);
        $em->flush();

        return $user;
    }

    private function updateUserDocumentContent(string $uploadedFilePath, User $user, UserDocument $userDocument): void
    {
        try {
            $PDFParser = new Parser();
            $pdf = $PDFParser->parseFile($uploadedFilePath);

            if ('' === $content = Encoding::fixUTF8($pdf->getText())) {
                throw new \RuntimeException();
            }

            $userDocument->setContent($content);
        } catch (\Exception $e) {
            if ($user->getLastName() && $user->getFirstName()) {
                $userDocument->setContent($user->getLastName() . ' ' . $user->getFirstName());
            }
        }
    }
}
