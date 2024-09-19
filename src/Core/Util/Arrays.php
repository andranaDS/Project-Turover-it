<?php

namespace App\Core\Util;

class Arrays extends \Nette\Utils\Arrays
{
    public static function getRandomSubarray(array $array, int $min = 0, int $max = null): array
    {
        $count = \count($array);

        $max = min($count, $max ?? $count);
        $min = min(max(0, $min), $max);

        if (0 === $rand = mt_rand($min, $max)) {
            return [];
        }

        $randomIndexes = array_rand($array, $rand);
        $randomIndexes = \is_array($randomIndexes) ? $randomIndexes : [$randomIndexes];

        return self::subarray($array, $randomIndexes);
    }

    // @phpstan-ignore-next-line
    public static function getRandom(array $array)
    {
        return empty($array) ? null : $array[array_rand($array)];
    }

    public static function subarray(array $array, array $keys): array
    {
        return array_intersect_key($array, array_flip($keys));
    }

    public static function isAssoc(array $array): bool
    {
        return 0 === \count(array_filter(array_keys($array), 'is_string'));
    }
}
