<?php

namespace App\Sync\Transformer\JobPosting;

use App\Core\Util\Strings;
use App\JobPosting\Enum\DurationPeriod;

class DurationTransformer
{
    public const INDEX_PERIOD = 'period';
    public const INDEX_VALUE = 'value';

    public static function transform(?string $inValue, ?string &$error = null): ?array
    {
        if (empty($inValue)) {
            return null;
        }

        if (null !== $outValue = self::strToValue($inValue)) {
            return self::optimizePeriodAndValue($outValue);
        }

        $error = sprintf('"%s" is not a valid duration', $inValue);

        return null;
    }

    public static function strToValueRegex(string $string): ?array
    {
        $nb = $period = null;
        $matches = [];

        // first regex
        preg_match_all(sprintf('/(\\d+).*?(%s)/', implode('|', ['day', 'jour', 'week', 'semaine', 'month', 'mois', 'year', 'année', 'annee', 'an'])), $string, $matches);
        $matches = array_filter($matches);

        if (empty($matches)) {
            preg_match_all(sprintf('/(\\d+)(%s)/', implode('|', ['d', 'j', 'w', 's', 'm', 'y', 'a'])), $string, $matches);
            $matches = array_filter($matches);
        }

        if (!empty($matches)) {
            $nb = $matches[1][0] ?? null;
            $period = $matches[2][0] ?? null;
        }

        if (null !== $nb && null !== $period) {
            if (0 === $nb = (int) $nb) {
                return null;
            }

            if (null === $match = self::matchPeriodAndMultiplier($period)) {
                return null;
            }

            return [
                self::INDEX_PERIOD => $match[0],
                self::INDEX_VALUE => $nb * $match[1],
            ];
        }

        return null;
    }

    public static function strToValueHardcoded(string $string): ?array
    {
        $hardcodedMatches = [
            'longue' => [DurationPeriod::YEAR, 1],
            'long' => [DurationPeriod::YEAR, 1],
            'langue' => [DurationPeriod::YEAR, 1],
            'courte' => [DurationPeriod::MONTH, 3],
            'short' => [DurationPeriod::MONTH, 3],
        ];

        foreach ($hardcodedMatches as $needle => $datum) {
            if (Strings::contains($string, $needle)) {
                return [
                    self::INDEX_PERIOD => $datum[0],
                    self::INDEX_VALUE => $datum[1],
                ];
            }
        }

        return null;
    }

    public static function strToValue(string $string): ?array
    {
        $string = mb_strtolower($string, 'utf8');

        if (null !== $value = self::strToValueRegex($string)) {
            return $value;
        }

        if (null !== $value = self::strToValueHardcoded($string)) {
            return $value;
        }

        return null;
    }

    /**
     * Returns the period and its multiplier.
     */
    private static function matchPeriodAndMultiplier(string $string): ?array
    {
        return match ($string) {
            'day', 'jour', 'd', 'j' => [DurationPeriod::DAY, 1],
            'month', 'mois', 'm' => [DurationPeriod::MONTH, 1],
            'year', 'année', 'annee', 'an', 'y', 'a' => [DurationPeriod::YEAR, 1],
            'week', 'semaine', 'w', 's' => [DurationPeriod::DAY, 7],
            default => null
        };
    }

    private static function optimizePeriodAndValue(array $data): array
    {
        if (DurationPeriod::DAY === $data[self::INDEX_PERIOD] && 0 === $data[self::INDEX_VALUE] % 30) {
            $data = [self::INDEX_PERIOD => DurationPeriod::MONTH, self::INDEX_VALUE => $data[self::INDEX_VALUE] / 30];
        }

        if (DurationPeriod::MONTH === $data[self::INDEX_PERIOD] && 0 === $data[self::INDEX_VALUE] % 12) {
            $data = [self::INDEX_PERIOD => DurationPeriod::YEAR, self::INDEX_VALUE => $data[self::INDEX_VALUE] / 12];
        }

        return $data;
    }
}
