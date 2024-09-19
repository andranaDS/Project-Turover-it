<?php

namespace App\Sync\Transformer\JobPosting;

use App\Core\Util\Strings;
use App\JobPosting\Enum\ExperienceLevel;

class ExperienceLevelTransformer
{
    public static function transform(?string $inValue, ?string &$error = null): ?string
    {
        if (empty($inValue)) {
            return null;
        }

        // by text
        $hardcodedMatches = [
            'debutant' => ExperienceLevel::JUNIOR,
        ];
        foreach ($hardcodedMatches as $needle => $experienceLevel) {
            if (Strings::contains($inValue, $needle)) {
                return $experienceLevel;
            }
        }

        // by string
        $matches = [];
        preg_match('/[0-9]+/', $inValue, $matches);
        $outValue = $matches[0] ?? null;

        if (null === $outValue) {
            $error = sprintf('"%s" is not a valid level experience', $inValue);

            return null;
        }

        $outValue = (int) $outValue;

        if ($outValue <= 1) {
            // 0-1
            $outValue = ExperienceLevel::JUNIOR;
        } elseif ($outValue <= 4) {
            // 1-4
            $outValue = ExperienceLevel::INTERMEDIATE;
        } elseif ($outValue <= 9) {
            // 4-9
            $outValue = ExperienceLevel::SENIOR;
        } else {
            // 10+
            $outValue = ExperienceLevel::EXPERT;
        }

        return $outValue;
    }
}
