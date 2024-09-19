<?php

namespace App\Sync\Transformer\JobPosting;

use App\Core\Util\Strings;
use Carbon\Carbon;

class StartsAtTransformer
{
    public static function transform(?string $inValue, ?\DateTime $relative = null, ?string &$error = null): ?\DateTime
    {
        if (empty($inValue)) {
            return null;
        }

        // words assimilated to null
        $words = [
            'asap', 'aap', 'as soon as possible',
            'dès que possible', 'des que possible', 'selon possi',
            'immédiate', 'immediate', 'sous 1 mois', 'immédiat',
            'flexible', 'rapidement', 'urgent', 'selon vos disponibilités',
            'plus vite', 'plus tôt',
        ];
        foreach ($words as $word) {
            if (Strings::contains($inValue, $word)) {
                return null;
            }
        }

        // generic
        if (null !== $outValue = self::strToValue($inValue, $relative)) {
            return $outValue;
        }

        $error = sprintf('"%s" is not a valid startsAt', $inValue);

        return null;
    }

    private static function strToValueRegexMonth(string $string, \DateTime $relative): ?\DateTime
    {
        $conf = [
            'january' => 1, 'janvier' => 1, 'janv' => 1,
            'february' => 2, 'fevrier' => 2, 'février' => 2, 'feb' => 2, 'fev' => 2,
            'march' => 3, 'mars' => 3,
            'april' => 4, 'avril' => 4,
            'may' => 5, 'mai' => 5,
            'june' => 6, 'juin' => 6,
            'july' => 7, 'juillet' => 7,
            'august' => 8, 'aout' => 8, 'août' => 8,
            'september' => 9, 'septembre' => 9, 'sept' => 9,
            'october' => 10, 'octobre' => 10, 'oct' => 10,
            'november' => 11, 'novembre' => 11, 'nov' => 11,
            'december' => 12, 'decembre' => 12, 'décembre' => 12, 'dec' => 12,
        ];

        $matches = [];

        if (preg_match_all(sprintf('/(%s).*?(\\d{2,4})/', implode('|', array_keys($conf))), $string, $matches)) {
            $month = $conf[$matches[1][0]];
            $year = $matches[2][0];
            if (2 === \strlen($year)) {
                $year = (int) (($year > '30' ? '19' : '20') . $year);
            }

            return (new \DateTime())->setDate($year, $month, 1)->setTime(0, 0);
        }

        if (preg_match_all(sprintf('/(%s)/', implode('|', array_keys($conf))), $string, $matches)) {
            $month = $conf[$matches[1][0]];
            $current = Carbon::createFromDate((int) $relative->format('Y'), $month, 1);
            $current->firstOfMonth();

            if ($current < $relative) {
                $previous = $current;
                $next = (clone $previous)->modify('+ 1 year');
            } else {
                $next = $current;
                $previous = (clone $next)->modify('- 1 year');
            }

            $nbDays = 60;
            $limit = (clone $relative)->modify(sprintf('- %d days', $nbDays));

            if ($previous > $limit) {
                return $previous;
            }

            return $next;
        }

        return null;
    }

    private static function strToValueRegexDate(string $string): ?\DateTime
    {
        $conf = [
            '/\\d{1,2}\\/\\d{1,2}\\/\\d{4}/' => 'd/m/Y',
            '/\\d{1,2}\\/\\d{1,2}\\/\\d{2}/' => 'd/m/y',
            '/\\d{1,2}\\/\\d{4}/' => 'm/Y',
            '/\\d{1,2}\\/\\d{2}/' => 'm/y',
            '/\\d{4}-d{1,2}-d{1,2}/' => 'Y-m-d',
            '/\\d{2}-\\d{1,2}-\\d{1,2}/' => 'y-m-d',
            '/\\d{4}-d{1,2}/' => 'Y-m',
            '/\\d{2}-\\d{1,2}/' => 'y-m',
        ];

        foreach ($conf as $regex => $format) {
            $matches = [];
            if (preg_match_all($regex, $string, $matches)) {
                $value = $matches[0][0];

                if (\in_array($format, ['y-m', 'Y-m'], true)) {
                    $format = "$format-d";
                    $value = "$value-01";
                } elseif (\in_array($format, ['m/y', 'm/Y'], true)) {
                    $format = "d-$format";
                    $value = "01-$value";
                }

                if (false !== $date = \DateTime::createFromFormat($format, $value)) {
                    $date->setTime(0, 0);

                    return $date;
                }

                return null;
            }
        }

        return null;
    }

    private static function strToValue(string $string, ?\DateTime $relative = null): ?\DateTime
    {
        $string = mb_strtolower($string, 'utf8');
        if (null === $relative) {
            $relative = new \DateTime('now');
        }
        $relative->setTime(0, 0);

        if (null !== $value = self::strToValueRegexMonth($string, $relative)) {
            return $value;
        }

        if (null !== $value = self::strToValueRegexDate($string)) {
            return $value;
        }

        return null;
    }
}
