<?php

namespace App\User\Tests\Unit\Validator;

use App\Core\Validator\PasswordComplexity;
use PHPUnit\Framework\TestCase;

class PasswordComplexityTest extends TestCase
{
    public function testConstructor(): void
    {
        $subject = new PasswordComplexity();
        self::assertSame(2, $subject->minScore);

        $subject = new PasswordComplexity([
            'minScore' => 3,
        ]);
        self::assertSame(3, $subject->minScore);

        $subject = new PasswordComplexity([
            'minScore' => 0,
        ]);
        self::assertSame(0, $subject->minScore);
    }
}
