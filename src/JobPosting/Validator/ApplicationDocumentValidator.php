<?php

namespace App\JobPosting\Validator;

use App\JobPosting\Entity\ApplicationDocument as ApplicationDocumentEntity;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ApplicationDocumentValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ApplicationDocument) {
            throw new UnexpectedTypeException($constraint, ApplicationDocument::class);
        }

        if (!$value instanceof Collection) {
            throw new UnexpectedTypeException($constraint, Collection::class);
        }

        $resumeDocuments = $value->filter(static function (ApplicationDocumentEntity $document) {
            return null !== $document->getDocument() && $document->getDocument()->getResume();
        });

        if (0 === $resumeDocuments->count()) {
            $this->context
                ->buildViolation($constraint->noResumeDocument)
                ->addViolation()
            ;

            return;
        }

        $defaultResumeDocument = $resumeDocuments->filter(static function (ApplicationDocumentEntity $document) {
            return null !== $document->getDocument() && $document->getDocument()->getDefaultResume();
        })->first();

        if (false === $defaultResumeDocument) {
            $this->context
                ->buildViolation($constraint->noDefaultResumeDocument)
                ->addViolation()
            ;
        }
    }
}
