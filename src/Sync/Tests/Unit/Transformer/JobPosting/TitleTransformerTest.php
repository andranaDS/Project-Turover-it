<?php

namespace App\Sync\Tests\Unit\Transformer\JobPosting;

use App\Sync\Transformer\JobPosting\TitleTransformer;
use PHPUnit\Framework\TestCase;

class TitleTransformerTest extends TestCase
{
    public function testTransform(): void
    {
        self::assertSame('(certified) Développeur Symfony', TitleTransformer::transform('(certified) Développeur Symfony', $error));
        self::assertSame('Développeur Symfony (certified)', TitleTransformer::transform('Développeur Symfony (certified)', $error));
        self::assertSame('Développeur Symfony', TitleTransformer::transform('Développeur Symfony (URGENT)'));
        self::assertSame('Développeur Symfony', TitleTransformer::transform('Développeur Symfony - URGENT'));
        self::assertSame('Développeur Symfony', TitleTransformer::transform('Développeur Symfony - TRÈS URGENT'));
        self::assertSame('Développeur Symfony', TitleTransformer::transform('Développeur Symfony - Urgent'));
        self::assertSame('Développeur Symfony', TitleTransformer::transform('Développeur Symfony - Très Urgent'));
        self::assertSame('Développeur Symfony', TitleTransformer::transform('Développeur Symfony'));
        self::assertNull(TitleTransformer::transform(' - '));
        self::assertNull(TitleTransformer::transform('  '));
        self::assertNull(TitleTransformer::transform(' '));
        self::assertNull(TitleTransformer::transform(''));
    }
}
