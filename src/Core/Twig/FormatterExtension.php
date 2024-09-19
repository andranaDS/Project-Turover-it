<?php

namespace App\Core\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class FormatterExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('array_string', [$this, 'arrayToString']),
        ];
    }

    public function arrayToString(array $array): string
    {
        return '<pre>' . print_r($array, true) . '</pre>'; // @phpstan-ignore-line
    }
}
