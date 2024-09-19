<?php

namespace App\Resource\Enum;

use Greg0ire\Enum\AbstractEnum;

final class Location extends AbstractEnum
{
    public const ILE_DE_FRANCE = 'ile_de_france';
    public const LARGE_CITIES = 'large_cities';
    public const SMALL_CITIES = 'small_cities';
    public const OUTSIDE_FRANCE = 'outside_france';
}
