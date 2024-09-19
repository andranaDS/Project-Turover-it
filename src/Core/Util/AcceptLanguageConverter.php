<?php

namespace App\Core\Util;

use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;

class AcceptLanguageConverter
{
    public const LANGUAGE_ALIASES = [
        'fr' => 'fr-fr',
    ];

    public static function extractAcceptLanguageFromRequest(Request $request): ?string
    {
        if (null === $acceptLanguage = $request->headers->get('Accept-Language')) {
            return null;
        }

        $acceptLanguage = HeaderUtils::split($acceptLanguage, ',;');

        return $acceptLanguage[0][0] ?? null;
    }

    public static function acceptLanguageToLocale(string $acceptLanguage): ?string
    {
        if (isset(self::LANGUAGE_ALIASES[$acceptLanguage])) {
            $acceptLanguage = self::LANGUAGE_ALIASES[$acceptLanguage];
        }

        $explodedAcceptLanguage = explode('-', $acceptLanguage);
        if (2 !== \count($explodedAcceptLanguage)) {
            return null;
        }

        return $explodedAcceptLanguage[0] . '_' . mb_strtoupper($explodedAcceptLanguage[1], 'utf8');
    }

    public static function localeToAcceptLanguage(string $locale): ?string
    {
        $explodedLocale = explode('_', $locale);
        if (2 !== \count($explodedLocale)) {
            return null;
        }

        $acceptLanguage = $explodedLocale[0] . '-' . mb_strtolower($explodedLocale[1], 'utf8');

        if (\in_array($acceptLanguage, self::LANGUAGE_ALIASES, true)) {
            $acceptLanguage = array_search($acceptLanguage, self::LANGUAGE_ALIASES, true);
        }

        return $acceptLanguage;
    }
}
