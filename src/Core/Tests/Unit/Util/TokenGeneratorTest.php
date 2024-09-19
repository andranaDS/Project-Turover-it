<?php

namespace App\Core\Tests\Unit\Util;

use App\Core\Util\TokenGenerator;
use PHPUnit\Framework\TestCase;

class TokenGeneratorTest extends TestCase
{
    public function testGenerate(): void
    {
        self::assertSame(0, \strlen(TokenGenerator::generate(0)));
        self::assertSame(1, \strlen(TokenGenerator::generate(1)));
        self::assertSame(2, \strlen(TokenGenerator::generate(2)));
        self::assertSame(2048, \strlen(TokenGenerator::generate(2048)));
        self::assertMatchesRegularExpression('/([a-zA-Z0-9])*/', TokenGenerator::generate(2048));
    }

    public function testGenerateFromValue(): void
    {
        self::assertSame('6ab91bcc5ef8a49a', TokenGenerator::generateFromValue('test'));
        self::assertSame('5ef8a49a', TokenGenerator::generateFromValue('test', 8));
        self::assertSame('789e193d3780c4c16ab91bcc5ef8a49a', TokenGenerator::generateFromValue('test', 32));
        self::assertSame('3dbbf289789e193d3780c4c16ab91bcc5ef8a49a', TokenGenerator::generateFromValue('test', 64));
    }
}
