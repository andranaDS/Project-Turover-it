<?php

namespace App\Core\Annotation;

/**
 * @Annotation()
 * @Target({"PROPERTY"})
 */
class ApiThumbnailUrls
{
    private array $filters;

    public function __construct(array $filters)
    {
        if (isset($filters['value'])) {
            $filters = (array) $filters['value'];
        }

        if (empty($filters)) {
            throw new \InvalidArgumentException(sprintf('Parameter of annotation "%s" cannot be empty.', static::class));
        }

        $this->filters = $filters;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }
}
