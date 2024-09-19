<?php

namespace App\User\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\MissingOptionsException;

/**
 * @Annotation()
 */
class UserJobs extends Constraint
{
    public ?int $max;
    public string $message = 'user.profile.jobs';

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }

    /**
     * {@inheritdoc}
     */
    public function __construct(
        array $options = []
    ) {
        $max = $options['max'] ?? null;

        unset($options['max']);

        parent::__construct($options);

        $this->max = $max;

        if (null === $this->max) {
            throw new MissingOptionsException(sprintf('Option "max" must be given for constraint "%s".', __CLASS__), ['max']);
        }
    }
}
