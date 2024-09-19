<?php

namespace App\Core\Tests\Unit\Util;

use App\Core\Util\ContentDetector;
use PHPUnit\Framework\TestCase;

class ContentDetectorTest extends TestCase
{
    public function testDetectText(): void
    {
        $value = 'Lorem ipsum dolor sit amet, consectetur lorem adipiscing elit.';
        self::assertSame(['lorem'], ContentDetector::detect($value, ['lorem']));
        self::assertSame(['lorem ipsum'], ContentDetector::detect($value, ['lorem ipsum']));
        self::assertSame(['consectetur', 'lorem'], ContentDetector::detect($value, ['lorem', 'consectetur']));
    }

    public function testDetectHtml(): void
    {
        $value = '<p>Lorem <strong>ipsum</strong> dolor sit amet, consectetur lorem adipiscing elit.</p>';
        self::assertSame(['lorem'], ContentDetector::detect($value, ['lorem']));
        self::assertSame(['lorem ipsum'], ContentDetector::detect($value, ['lorem ipsum']));
        self::assertSame(['consectetur', 'lorem'], ContentDetector::detect($value, ['lorem', 'consectetur']));
    }
}
