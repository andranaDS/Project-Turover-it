<?php

namespace App\Core\Annotation;

/**
 * @Annotation()
 * @Target({"PROPERTY"})
 */
class ApiFileUrl
{
    private string $property;

    public function __construct(array $parameters)
    {
        $property = $parameters['property'] ?? null;
        if (empty($property)) {
            throw new \InvalidArgumentException(sprintf('Parameter "property" of annotation "%s" cannot be empty.', static::class));
        }

        $this->property = $property;
    }

    public function getProperty(): string
    {
        return $this->property;
    }
}
