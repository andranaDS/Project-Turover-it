<?php

namespace App\Sync\Transformer\JobPosting;

use App\Core\Util\Strings;

class LocationTransformer
{
    public static function getAdminLevels2(): array
    {
        $data = [];
        if (($handle = fopen(__DIR__ . '/files/admin_level_2.csv', 'r')) !== false) {
            while (false !== ($d = fgetcsv($handle)) && \is_array($d)) {
                $data[$d[0]] = [
                    'code' => $d[0],
                    'name' => $d[1],
                    'adminLevel1Name' => $d[3],
                ];
            }
            fclose($handle);
        }

        return $data;
    }

    public static function getAdminLevel2(string $code): ?array
    {
        return self::getAdminLevels2()[$code] ?? null;
    }

    public static function transform(?string $inValue, ?string &$error = null): ?string
    {
        if (empty($inValue)) {
            return null;
        }

        $inValue = stripslashes($inValue);

        if (null !== $outValue = self::strHardcodedToValue($inValue)) {
            return $outValue;
        }

        if (null !== $outValue = self::strTurnoverToValue($inValue)) {
            return $outValue;
        }

        if (null !== $outValue = self::strCityToValue($inValue)) {
            return $outValue;
        }

        if (null !== $outValue = self::strAdminLevel2ToValue($inValue)) {
            return $outValue;
        }

        if (null !== $outValue = self::strAdminLevel1ToValue($inValue)) {
            return $outValue;
        }

        if (true === self::strAssimilatedToNull($inValue)) {
            return null;
        }

        return self::clean($inValue);
    }

    public static function clean(string $inValue): ?string
    {
        $words = ['proche', 'near', 'environ'];

        if (null === $inValue = preg_replace([
            '/\(/',
            '/\)/',
            sprintf('/(%s)/i', implode('|', $words)),
        ], '', $inValue)) {
            return null;
        }

        if (null === $inValue = preg_replace('/\s+/', ' ', $inValue)) {
            return null;
        }

        return trim($inValue);
    }

    public static function strTurnoverToValue(string $inValue): ?string
    {
        if (1 !== preg_match('/.+\(\d{2,3}\)/', $inValue)) {
            return null;
        }

        return preg_replace_callback(
            '/\\s+\\((\\d{2,3})\\)/',
            static function (array $matches) {
                $code = $matches[1];
                $adminLevel2 = self::getAdminLevel2($code) ?? null;

                return null === $adminLevel2 ? $code : sprintf(', %s, %s', $adminLevel2['name'], $adminLevel2['adminLevel1Name']);
            },
            $inValue
        );
    }

    public static function strAssimilatedToNull(string $inValue): bool
    {
        $values = ['télétravail', 'teletravail', 'remote', 'homeworking'];
        foreach ($values as $value) {
            if (Strings::contains(mb_strtolower($inValue, 'utf8'), $value)) {
                return true;
            }
        }

        return false;
    }

    public static function strCityToValue(string $inValue): ?string
    {
        $values = [
            'Paris',
            'Marseille',
            'Lyon',
            'Toulouse',
            'Nice',
            'Nantes',
            'Strasbourg',
            'Montpellier',
            'Bordeaux',
            'Lille',
            'Rennes',
            'Reims',
            'Le Havre',
            'Saint-Étienne',
            'Toulon',
            'Grenoble',
            'Dijon',
            'Nîmes',
            'Angers',
            'Villeurbanne',
            'Saint-Denis',
            'Le Mans',
            'Aix-en-Provence',
            'Clermont-Ferrand',
            'Brest',
            'Tours',
            'Limoges',
            'Amiens',
            'Perpignan',
            'Metz',
            'Roubaix',
        ];

        // remove deceptive words
        $valuesRegex = implode('|', $values);
        if (null === $inValue = preg_replace([
            sprintf('/proche\s(%s)/i', $valuesRegex),
            sprintf('/(%s)\s(sud|nord|est|ouest)/i', $valuesRegex),
        ], '', $inValue)) {
            return null;
        }

        // check
        foreach ($values as $value) {
            if (Strings::contains(mb_strtolower($inValue, 'utf8'), mb_strtolower($value, 'utf8'))) {
                return $value;
            }
        }

        return null;
    }

    public static function strHardcodedToValue(string $inValue): ?string
    {
        $values = [
            'La Défense (92)' => 'La Défense, Hauts-de-Seine, Île-de-France',
        ];

        foreach ($values as $key => $value) {
            if (1 === preg_match(sprintf('/\b%s\b/i', $key), $inValue)) {
                return $value;
            }
        }

        return null;
    }

    public static function strAdminLevel1ToValue(string $inValue): ?string
    {
        $values = [
            'IDF' => 'Île-de-France',
            'HDF' => 'Hauts-de-France',
            'PACA' => 'Provence-Alpes-Côte d\'Azur',
        ];

        foreach ($values as $key => $value) {
            if (1 === preg_match(sprintf('/\b%s\b/i', $key), $inValue)) {
                return $value;
            }
        }

        return null;
    }

    public static function strAdminLevel2ToValue(string $inValue): ?string
    {
        $matches = [];
        preg_match('/\b\d{2,3}\b/', $inValue, $matches);
        if (empty($matches)) {
            return null;
        }

        $code = $matches[0];

        $adminLevel2 = self::getAdminLevel2($code) ?? null;
        if (null === $adminLevel2) {
            return null;
        }

        return sprintf('%s, %s', $adminLevel2['name'], $adminLevel2['adminLevel1Name']);
    }
}
