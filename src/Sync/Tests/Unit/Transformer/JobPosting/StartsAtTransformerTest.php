<?php

namespace App\Sync\Tests\Unit\Transformer\JobPosting;

use App\Sync\Transformer\JobPosting\StartsAtTransformer;
use PHPUnit\Framework\TestCase;

class StartsAtTransformerTest extends TestCase
{
    public function testTransform(): void
    {
        // null

        // date
        self::assertSame(
            (new \DateTime('2026-11-01 00:00:00'))->format('Y-m-d H:i:s'),
            StartsAtTransformer::transform('26-11')->format('Y-m-d H:i:s')
        );
        self::assertSame(
            (new \DateTime('2026-11-01 00:00:00'))->format('Y-m-d H:i:s'),
            StartsAtTransformer::transform('2026-11')->format('Y-m-d H:i:s')
        );
        self::assertSame(
            (new \DateTime('2026-11-15 00:00:00'))->format('Y-m-d H:i:s'),
            StartsAtTransformer::transform('26-11-15')->format('Y-m-d H:i:s')
        );
        self::assertSame(
            (new \DateTime('2026-11-15 00:00:00'))->format('Y-m-d H:i:s'),
            StartsAtTransformer::transform('2026-11-15')->format('Y-m-d H:i:s')
        );

        self::assertSame(
            (new \DateTime('2025-04-01 00:00:00'))->format('Y-m-d H:i:s'),
            StartsAtTransformer::transform('04/25')->format('Y-m-d H:i:s')
        );
        self::assertSame(
            (new \DateTime('2025-04-01 00:00:00'))->format('Y-m-d H:i:s'),
            StartsAtTransformer::transform('04/2025')->format('Y-m-d H:i:s')
        );
        self::assertSame(
            (new \DateTime('2025-04-03 00:00:00'))->format('Y-m-d H:i:s'),
            StartsAtTransformer::transform('03/04/25')->format('Y-m-d H:i:s')
        );
        self::assertSame(
            (new \DateTime('2025-04-03 00:00:00'))->format('Y-m-d H:i:s'),
            StartsAtTransformer::transform('03/04/2025')->format('Y-m-d H:i:s')
        );

        // month without year
        self::assertSame(
            (new \DateTime('2021-11-01 00:00:00'))->format('Y-m-d H:i:s'),
            StartsAtTransformer::transform('novembre', new \DateTime('2021-01-15 00:00:00'))->format('Y-m-d H:i:s')
        );
        self::assertSame(
            (new \DateTime('2020-12-01 00:00:00'))->format('Y-m-d H:i:s'),
            StartsAtTransformer::transform('décembre', new \DateTime('2021-01-15 00:00:00'))->format('Y-m-d H:i:s')
        );
        self::assertSame(
            (new \DateTime('2021-01-01 00:00:00'))->format('Y-m-d H:i:s'),
            StartsAtTransformer::transform('janvier', new \DateTime('2021-01-15 00:00:00'))->format('Y-m-d H:i:s')
        );
        self::assertSame(
            (new \DateTime('2022-07-01 00:00:00'))->format('Y-m-d H:i:s'),
            StartsAtTransformer::transform('juillet', new \DateTime('2021-09-15 00:00:00'))->format('Y-m-d H:i:s')
        );
        self::assertSame(
            (new \DateTime('2021-08-01 00:00:00'))->format('Y-m-d H:i:s'),
            StartsAtTransformer::transform('aout', new \DateTime('2021-09-15 00:00:00'))->format('Y-m-d H:i:s')
        );
        self::assertSame(
            (new \DateTime('2021-09-01 00:00:00'))->format('Y-m-d H:i:s'),
            StartsAtTransformer::transform('septembre', new \DateTime('2021-09-15 00:00:00'))->format('Y-m-d H:i:s')
        );

        // month with year
        self::assertSame(
            (new \DateTime('1931-02-01 00:00:00'))->format('Y-m-d H:i:s'),
            StartsAtTransformer::transform('Février 31')->format('Y-m-d H:i:s')
        );
        self::assertSame(
            (new \DateTime('2022-02-01 00:00:00'))->format('Y-m-d H:i:s'),
            StartsAtTransformer::transform('Février 22')->format('Y-m-d H:i:s')
        );
        self::assertSame(
            (new \DateTime('2022-01-01 00:00:00'))->format('Y-m-d H:i:s'),
            StartsAtTransformer::transform('le 15 janvier 2022')->format('Y-m-d H:i:s')
        );
        self::assertSame(
            (new \DateTime('2021-01-01 00:00:00'))->format('Y-m-d H:i:s'),
            StartsAtTransformer::transform('12 january 2021')->format('Y-m-d H:i:s')
        );
        self::assertSame(
            (new \DateTime('2021-01-01 00:00:00'))->format('Y-m-d H:i:s'),
            StartsAtTransformer::transform('january 2021')->format('Y-m-d H:i:s')
        );
        self::assertSame(
            (new \DateTime('2021-01-01 00:00:00'))->format('Y-m-d H:i:s'),
            StartsAtTransformer::transform('janvier 2021')->format('Y-m-d H:i:s')
        );
        self::assertSame(
            (new \DateTime('2021-01-01 00:00:00'))->format('Y-m-d H:i:s'),
            StartsAtTransformer::transform('janv. 2021')->format('Y-m-d H:i:s')
        );
        self::assertSame(
            (new \DateTime('2021-01-01 00:00:00'))->format('Y-m-d H:i:s'),
            StartsAtTransformer::transform('janv 2021')->format('Y-m-d H:i:s')
        );
        self::assertSame(
            (new \DateTime('2022-01-01 00:00:00'))->format('Y-m-d H:i:s'),
            StartsAtTransformer::transform('january 2022')->format('Y-m-d H:i:s')
        );
        self::assertSame(
            (new \DateTime('2022-01-01 00:00:00'))->format('Y-m-d H:i:s'),
            StartsAtTransformer::transform('janvier 2022')->format('Y-m-d H:i:s')
        );
        self::assertSame(
            (new \DateTime('2022-01-01 00:00:00'))->format('Y-m-d H:i:s'),
            StartsAtTransformer::transform('janv. 2022')->format('Y-m-d H:i:s')
        );
        self::assertSame(
            (new \DateTime('2022-01-01 00:00:00'))->format('Y-m-d H:i:s'),
            StartsAtTransformer::transform('janv 2022')->format('Y-m-d H:i:s')
        );
    }
}
