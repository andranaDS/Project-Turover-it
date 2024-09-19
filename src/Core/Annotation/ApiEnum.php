<?php

namespace App\Core\Annotation;

/**
 * @Annotation()
 * @Target({"PROPERTY"})
 */
class ApiEnum
{
    private string $class;
    private string $translationDomain;

    public function __construct(array $parameters)
    {
        $class = $parameters['class'] ?? null;
        if (empty($class)) {
            throw new \InvalidArgumentException(sprintf('Parameter "class" of annotation "%s" cannot be empty.', static::class));
        }

        $translationDomain = $parameters['translationDomain'] ?? 'messages';
        if (empty($translationDomain)) {
            throw new \InvalidArgumentException(sprintf('Parameter "translationDomain" of annotation "%s" cannot be empty.', static::class));
        }

        $this->class = $class;
        $this->translationDomain = $translationDomain;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getTranslationDomain(): string
    {
        return $this->translationDomain;
    }
}
