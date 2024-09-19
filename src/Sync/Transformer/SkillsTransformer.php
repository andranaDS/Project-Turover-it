<?php

namespace App\Sync\Transformer;

use App\Core\Util\Arrays;
use Nette\Utils\Json;
use Nette\Utils\JsonException;

class SkillsTransformer
{
    public static function transform(?string $inValue, array $values = [], ?string &$error = null): array
    {
        if (empty($inValue)) {
            return [];
        }

        try {
            $inValue = array_values(array_unique(array_filter(Arrays::map(Json::decode($inValue, Json::FORCE_ARRAY), static function (array $s) {
                return mb_strtolower(trim($s['name']), 'utf-8');
            }))));
        } catch (JsonException $e) {
            $error = 'Json invalid';

            return [];
        }

        $inValueNotFound = array_values(array_diff($inValue, $values));
        $outValue = array_values(array_intersect($inValue, $values));

        if (!empty($inValueNotFound)) {
            $error = sprintf('No match found for ["%s"]', implode('", "', $inValueNotFound) . '"');
        }

        return $outValue;
    }
}
