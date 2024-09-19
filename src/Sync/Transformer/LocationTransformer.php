<?php

namespace App\Sync\Transformer;

use App\Core\Entity\Location;
use App\Core\Manager\LocationManager;
use App\Core\Util\Arrays;

class LocationTransformer
{
    public static function transform(?string $inValue, LocationManager $lm, ?string &$error = null): ?Location
    {
        if (empty($inValue)) {
            return null;
        }

        if (null !== $outValue = Arrays::first($lm->autocompleteMobilities($inValue))) {
            /* @var Location $outValue */
            $outValue->setValue($inValue);

            return $outValue;
        }

        $error = sprintf('Location "%s" is not a found', $inValue);

        return null;
    }
}
