<?php

namespace App\JobPosting\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation()
 */
class ApplicationDocument extends Constraint
{
    public string $noResumeDocument = 'application.no_resume_document';
    public string $noDefaultResumeDocument = 'application.no_default_resume_document';

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
