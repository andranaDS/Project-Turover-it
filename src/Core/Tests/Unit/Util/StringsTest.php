<?php

namespace App\Core\Tests\Unit\Util;

use App\Core\Util\Strings;
use PHPUnit\Framework\TestCase;

class StringsTest extends TestCase
{
    public function testStripTags(): void
    {
        self::assertSame('Hello world!', Strings::stripTags('Hello world!'));
        self::assertSame('Hello world!', Strings::stripTags('<h1>Hello world!</h1>'));
        self::assertSame('Hello world!', Strings::stripTags('<h1>Hello  world!</h1>'));
        self::assertSame('Hello world!', Strings::stripTags('<h1> Hello world!</h1>'));
        self::assertSame('Hello world!', Strings::stripTags('<h1>Hello world! </h1>'));
        self::assertSame('Hello world!', Strings::stripTags('<h1>  Hello world!  </h1>'));
        self::assertSame('Hello world!', Strings::stripTags("<h1>Hello \n world!</h1>"));
        self::assertSame('Hello world!', Strings::stripTags('<h1><h2>Hello world!</h2></h1>'));
        self::assertSame('Hello world!', Strings::stripTags('<h1><h2>Hello world!</h2><p> </p></h1>'));
    }

    public function testSubstrToLength(): void
    {
        $chars = 'Lorem ipsum dolor sit amet.';
        self::assertSame('Lorem ipsum dolor sit amet.', Strings::substrToLength($chars, 27));
        self::assertSame('Lorem ipsum dolor sit amet.', Strings::substrToLength($chars, 100));
        self::assertSame('Lorem...', Strings::substrToLength($chars, 10));
        self::assertSame('Lorem...', Strings::substrToLength($chars, 10, '...'));
        self::assertSame('Lorem', Strings::substrToLength($chars, 10, ''));
        self::assertSame('Lorem!', Strings::substrToLength($chars, 10, '!'));
        self::assertSame('Lorem ipsum...', Strings::substrToLength($chars, 14));
        self::assertSame('Lorem ipsum...', Strings::substrToLength($chars, 15));
        self::assertSame('Lorem ipsum...', Strings::substrToLength($chars, 16));
    }
}
