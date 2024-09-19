<?php

namespace App\Sync\Transformer\JobPosting;

use App\Core\Util\Arrays;

class SalaryTransformer
{
    public static function transform(?string $inValue, ?string &$error = null): ?array
    {
        if (empty($inValue)) {
            return null;
        }

        if (null === $salaryDetected = self::strToValues($inValue)) {
            $error = sprintf('"%s" is not a valid salary', $inValue);

            return null;
        }

        $salaryAuthorized = [
            'daily' => [
                'min' => 120,
                'max' => 2000,
            ],
            'annual' => [
                'min' => 10000,
                'max' => 200000,
            ],
        ];

        if ($salaryDetected['min'] >= $salaryAuthorized['daily']['min'] && $salaryDetected['max'] <= $salaryAuthorized['daily']['max']) {
            return [
                'minDailySalary' => $salaryDetected['min'],
                'maxDailySalary' => $salaryDetected['max'],
            ];
        }
        if ($salaryDetected['min'] >= $salaryAuthorized['annual']['min'] && $salaryDetected['max'] <= $salaryAuthorized['annual']['max']) {
            return [
                'minAnnualSalary' => $salaryDetected['min'],
                'maxAnnualSalary' => $salaryDetected['max'],
            ];
        }

        $error = sprintf('"%s" seems too high for a daily salary and too low for a annual one', $inValue);

        return null;
    }

    public static function strToValues(string $string): ?array
    {
        // transform k€
        $string = preg_replace_callback('/(\\d{1,3})\\s?k\\s?(eur|€)?/ui', function (array $matches) {
            return ((int) $matches[1] * 1000) . ' ';
        }, $string);

        if (empty($string)) {
            return null;
        }

        $matches = [];
        preg_match_all('/(\\d{2,6})(\\.\\d{0,2})?/', $string, $matches);

        $matches = array_filter(Arrays::map($matches[0] ?? [], function (string $value) {
            return (int) $value;
        }));

        if (empty($matches)) {
            return null;
        }

        $matches = \array_slice($matches, 0, 2);
        sort($matches);
        $matchesCount = \count($matches);

        $min = $matches[0];
        if (1 === $matchesCount) {
            $max = $matches[0];
        } else {
            $max = $matches[1];
        }

        if ($min * 1000 < $max) {
            $min *= 1000;
        }

        if ($min < 120 && $max < 120) {
            $min *= 1000;
            $max *= 1000;
        }

        return [
            'min' => $min,
            'max' => $max,
        ];
    }
}
