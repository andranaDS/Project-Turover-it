<?php

namespace App\Core\Serializer;

use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer as APIPlatformDateTimeNormalizer;

class DateTimeNormalizer extends APIPlatformDateTimeNormalizer
{
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        if (null === $data || '' === $data) {
            return null; /* @phpstan-ignore-line */
        }

        return parent::denormalize($data, $type, $format, $context);
    }
}
