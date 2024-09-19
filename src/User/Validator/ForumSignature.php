<?php

namespace App\User\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation()
 */
class ForumSignature extends Constraint
{
    public string $message = 'user.signature.max_lines';
    public int $maxLines = 3;

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
