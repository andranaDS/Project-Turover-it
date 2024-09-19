<?php

namespace App\Core\Util;

class Numbers
{
    public static function formatCurrency(?float $value, string $currency = 'EUR', string $locale = 'fr'): ?string
    {
        if (null === $value) {
            return null;
        }

        $fmt = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);
        $fmt->setAttribute(\NumberFormatter::FRACTION_DIGITS, 0);

        $value = $fmt->formatCurrency($value, $currency);

        return preg_replace("/(\u{202f}|,){1}000/", 'k', $value);
    }

    public static function formatNumber(?float $value, string $locale = 'fr'): ?string
    {
        if (null === $value) {
            return null;
        }

        $fmt = new \NumberFormatter($locale, \NumberFormatter::DECIMAL);
        $fmt->setAttribute(\NumberFormatter::FRACTION_DIGITS, 0);

        $value = $fmt->format($value);

        if (false === \is_string($value)) {
            return null;
        }

        return preg_replace("/(\u{202f}|,){1}000/", 'k', $value);
    }

    public static function formatRangeCurrency(?float $minValue, ?float $maxValue, string $currency = 'EUR', string $locale = 'fr'): ?string
    {
        if ($minValue === $maxValue) {
            $maxValue = null;
        }

        if (null !== $minValue && null !== $maxValue) {
            $fmt = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);
            $fmt->setAttribute(\NumberFormatter::FRACTION_DIGITS, 0);

            $value = $fmt->formatCurrency(111, $currency);

            return str_replace('111', sprintf(
                '%s-%s',
                self::formatNumber($minValue, $locale),
                self::formatNumber($maxValue, $locale),
            ), $value);
        }

        if (null !== $minValue && null === $maxValue) {
            return self::formatCurrency($minValue, $currency, $locale);
        }

        return null;
    }
}
