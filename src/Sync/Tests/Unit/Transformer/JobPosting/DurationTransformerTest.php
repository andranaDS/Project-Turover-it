<?php

namespace App\Sync\Tests\Unit\Transformer\JobPosting;

use App\Sync\Transformer\JobPosting\DurationTransformer;
use PHPUnit\Framework\TestCase;

class DurationTransformerTest extends TestCase
{
    public function testTransform(): void
    {
        self::assertSame(['period' => 'year', 'value' => 2], DurationTransformer::transform('2 à 3 ans'));
        self::assertSame(['period' => 'year', 'value' => 2], DurationTransformer::transform('2a'));
        self::assertSame(['period' => 'year', 'value' => 1], DurationTransformer::transform('1y'));
        self::assertSame(['period' => 'month', 'value' => 4], DurationTransformer::transform('4m'));
        self::assertSame(['period' => 'month', 'value' => 6], DurationTransformer::transform('6m'));
        self::assertSame(['period' => 'day', 'value' => 28], DurationTransformer::transform('4w'));
        self::assertSame(['period' => 'day', 'value' => 56], DurationTransformer::transform('8w'));
        self::assertSame(['period' => 'day', 'value' => 28], DurationTransformer::transform('4s'));
        self::assertSame(['period' => 'day', 'value' => 56], DurationTransformer::transform('8s'));
        self::assertSame(['period' => 'month', 'value' => 5], DurationTransformer::transform('150d'));
        self::assertSame(['period' => 'day', 'value' => 61], DurationTransformer::transform('61d'));
        self::assertSame(['period' => 'month', 'value' => 2], DurationTransformer::transform('60d'));
        self::assertSame(['period' => 'day', 'value' => 31], DurationTransformer::transform('31j'));
        self::assertSame(['period' => 'month', 'value' => 1], DurationTransformer::transform('30j'));
        self::assertSame(['period' => 'day', 'value' => 1], DurationTransformer::transform('1 jour'));
        self::assertSame(['period' => 'day', 'value' => 15], DurationTransformer::transform('15 jours'));
        self::assertSame(['period' => 'day', 'value' => 32], DurationTransformer::transform('32 jours'));
        self::assertSame(['period' => 'month', 'value' => 1], DurationTransformer::transform('30 jours'));
        self::assertSame(['period' => 'month', 'value' => 2], DurationTransformer::transform('60 jours'));
        self::assertSame(['period' => 'day', 'value' => 65], DurationTransformer::transform('65 jours'));
        self::assertSame(['period' => 'month', 'value' => 6], DurationTransformer::transform('180 jours'));
        self::assertSame(['period' => 'day', 'value' => 184], DurationTransformer::transform('184 jours'));
        self::assertSame(['period' => 'day', 'value' => 1], DurationTransformer::transform('1 day'));
        self::assertSame(['period' => 'day', 'value' => 15], DurationTransformer::transform('15 days'));
        self::assertSame(['period' => 'day', 'value' => 31], DurationTransformer::transform('31 days'));
        self::assertSame(['period' => 'month', 'value' => 1], DurationTransformer::transform('30 days'));
        self::assertSame(['period' => 'month', 'value' => 2], DurationTransformer::transform('60 days'));
        self::assertSame(['period' => 'day', 'value' => 182], DurationTransformer::transform('182 days'));
        self::assertSame(['period' => 'month', 'value' => 6], DurationTransformer::transform('180 days'));
        self::assertSame(['period' => 'day', 'value' => 7], DurationTransformer::transform('1 semaine'));
        self::assertSame(['period' => 'day', 'value' => 28], DurationTransformer::transform('4 semaines'));
        self::assertSame(['period' => 'day', 'value' => 49], DurationTransformer::transform('7 semaines'));
        self::assertSame(['period' => 'day', 'value' => 56], DurationTransformer::transform('8 semaines'));
        self::assertSame(['period' => 'day', 'value' => 63], DurationTransformer::transform('9 semaines'));
        self::assertSame(['period' => 'day', 'value' => 84], DurationTransformer::transform('12 semaines'));
        self::assertSame(['period' => 'day', 'value' => 7], DurationTransformer::transform('1 week'));
        self::assertSame(['period' => 'day', 'value' => 28], DurationTransformer::transform('4 weeks'));
        self::assertSame(['period' => 'day', 'value' => 49], DurationTransformer::transform('7 weeks'));
        self::assertSame(['period' => 'day', 'value' => 56], DurationTransformer::transform('8 weeks'));
        self::assertSame(['period' => 'day', 'value' => 63], DurationTransformer::transform('9 weeks'));
        self::assertSame(['period' => 'day', 'value' => 84], DurationTransformer::transform('12 weeks'));
        self::assertSame(['period' => 'year', 'value' => 1], DurationTransformer::transform('1 an'));
        self::assertSame(['period' => 'year', 'value' => 1], DurationTransformer::transform('1 année'));
        self::assertSame(['period' => 'year', 'value' => 1], DurationTransformer::transform('1 year'));
        self::assertSame(['period' => 'year', 'value' => 2], DurationTransformer::transform('2 ans'));
        self::assertSame(['period' => 'year', 'value' => 2], DurationTransformer::transform('2 années'));
        self::assertSame(['period' => 'year', 'value' => 2], DurationTransformer::transform('2 ans'));
        self::assertSame(['period' => 'year', 'value' => 2], DurationTransformer::transform('2 years'));
        self::assertSame(['period' => 'year', 'value' => 2], DurationTransformer::transform('2  years'));
        self::assertSame(['period' => 'year', 'value' => 2], DurationTransformer::transform('2+ years'));
        self::assertSame(['period' => 'year', 'value' => 2], DurationTransformer::transform('2 longues années'));
        self::assertSame(['period' => 'month', 'value' => 1], DurationTransformer::transform('1 mois'));
        self::assertSame(['period' => 'month', 'value' => 1], DurationTransformer::transform('1 month'));
        self::assertSame(['period' => 'month', 'value' => 2], DurationTransformer::transform('2 mois'));
        self::assertSame(['period' => 'month', 'value' => 2], DurationTransformer::transform('2 months'));
        self::assertSame(['period' => 'month', 'value' => 2], DurationTransformer::transform('2+  months'));
        self::assertSame(['period' => 'year', 'value' => 1], DurationTransformer::transform('longue'));
        self::assertSame(['period' => 'year', 'value' => 1], DurationTransformer::transform('longue duree'));
        self::assertSame(['period' => 'year', 'value' => 1], DurationTransformer::transform('langue'));
        self::assertSame(['period' => 'year', 'value' => 1], DurationTransformer::transform('long term'));
        self::assertSame(['period' => 'month', 'value' => 3], DurationTransformer::transform('courte'));
        self::assertSame(['period' => 'month', 'value' => 3], DurationTransformer::transform('courte duree'));
        self::assertSame(['period' => 'month', 'value' => 3], DurationTransformer::transform('short term'));
        self::assertSame(['period' => 'year', 'value' => 2], DurationTransformer::transform('720d'));
    }
}
