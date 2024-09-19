<?php

namespace App\Core\Tests\Unit\Util;

use App\Core\Util\Arrays;
use PHPUnit\Framework\TestCase;

class ArraysTest extends TestCase
{
    public function testGetRandomSubarray(): void
    {
        $items = ['a', 'b', 'c', 'd', 'e'];
        $itemsCount = \count($items);
        for ($i = 0; $i < 1000; ++$i) {
            $subitems = Arrays::getRandomSubarray($items);
            $subitemsCount = \count($subitems);
            self::assertGreaterThanOrEqual(0, $subitemsCount);
            self::assertLessThanOrEqual($itemsCount, $subitemsCount);
        }
    }

    public function testGetRandomSubarrayWithMin(): void
    {
        $items = ['a', 'b', 'c', 'd', 'e'];

        for ($i = 0; $i < 1000; ++$i) {
            $subitems = Arrays::getRandomSubarray($items, 2);
            $subitemsCount = \count($subitems);
            self::assertGreaterThanOrEqual(2, $subitemsCount);
            self::assertLessThanOrEqual(5, $subitemsCount);
        }

        for ($i = 0; $i < 1000; ++$i) {
            $subitems = Arrays::getRandomSubarray($items, 5);
            $subitemsCount = \count($subitems);
            self::assertGreaterThanOrEqual(5, $subitemsCount);
            self::assertLessThanOrEqual(5, $subitemsCount);
        }

        for ($i = 0; $i < 1000; ++$i) {
            $subitems = Arrays::getRandomSubarray($items, 10);
            $subitemsCount = \count($subitems);
            self::assertGreaterThanOrEqual(5, $subitemsCount);
            self::assertLessThanOrEqual(5, $subitemsCount);
        }
    }

    public function testGetRandomSubarrayWithMax(): void
    {
        $items = ['a', 'b', 'c', 'd', 'e'];

        for ($i = 0; $i < 1000; ++$i) {
            $subitems = Arrays::getRandomSubarray($items, 0, 2);
            $subitemsCount = \count($subitems);
            self::assertGreaterThanOrEqual(0, $subitemsCount);
            self::assertLessThanOrEqual(2, $subitemsCount);
        }

        for ($i = 0; $i < 1000; ++$i) {
            $subitems = Arrays::getRandomSubarray($items, 0, 5);
            $subitemsCount = \count($subitems);
            self::assertGreaterThanOrEqual(0, $subitemsCount);
            self::assertLessThanOrEqual(5, $subitemsCount);
        }

        for ($i = 0; $i < 1000; ++$i) {
            $subitems = Arrays::getRandomSubarray($items, 0, 10);
            $subitemsCount = \count($subitems);
            self::assertGreaterThanOrEqual(0, $subitemsCount);
            self::assertLessThanOrEqual(5, $subitemsCount);
        }
    }

    public function testGetRandomSubarrayWithMinAndMax(): void
    {
        $items = ['a', 'b', 'c', 'd', 'e'];

        for ($i = 0; $i < 1000; ++$i) {
            $subitems = Arrays::getRandomSubarray($items, 1, 2);
            $subitemsCount = \count($subitems);
            self::assertGreaterThanOrEqual(1, $subitemsCount);
            self::assertLessThanOrEqual(2, $subitemsCount);
        }

        for ($i = 0; $i < 1000; ++$i) {
            $subitems = Arrays::getRandomSubarray($items, 3, 2);
            $subitemsCount = \count($subitems);
            self::assertGreaterThanOrEqual(2, $subitemsCount);
            self::assertLessThanOrEqual(2, $subitemsCount);
        }
    }

    public function testGetRandom(): void
    {
        $items = ['a', 'b', 'c'];
        self::assertContains(Arrays::getRandom($items), $items);

        $items = [1, 2];
        self::assertContains(Arrays::getRandom($items), $items);

        $items = [['a', 'b', 'c'], [1, 2]];
        self::assertContains(Arrays::getRandom($items), $items);

        $items = [['a', 'b', 'c'], [1, 2], 'aa'];
        self::assertContains(Arrays::getRandom($items), $items);

        self::assertNull(Arrays::getRandom([]));
    }

    public function testIsAssoc(): void
    {
        self::assertTrue(Arrays::isAssoc(['red', 'blue']));
        self::assertTrue(Arrays::isAssoc([0 => 'red', 1 => 'green', 2 => 'blue']));
        self::assertTrue(Arrays::isAssoc([0 => 'red', 2 => 'blue']));
        self::assertFalse(Arrays::isAssoc(['color' => 'red']));
        self::assertFalse(Arrays::isAssoc(['green', 'color' => 'red']));
    }

    public function testSubarray(): void
    {
        self::assertSame([1 => 'blue'], Arrays::subarray(['red', 'blue', 'green'], [1]));
        self::assertSame([0 => 'red', 1 => 'blue'], Arrays::subarray(['red', 'blue', 'green'], [0, 1]));
        self::assertSame([1 => 'blue', 2 => 'green'], Arrays::subarray(['red', 'blue', 'green'], [1, 2]));
        self::assertSame([0 => 'red', 1 => 'blue', 2 => 'green'], Arrays::subarray(['red', 'blue', 'green'], [0, 1, 2]));
        self::assertSame([0 => 'red', 2 => 'green'], Arrays::subarray(['red', 'blue', 'green'], [0, 'blue', 2]));
        self::assertSame([0 => 'red', 2 => 'green'], Arrays::subarray(['red', 'blue', 'green'], [0, 'blue', 2]));
        self::assertSame(['blue' => 'orange'], Arrays::subarray(['red' => 'apple', 'blue' => 'orange', 'green' => 'pineapple'], ['blue']));
        self::assertSame(['blue' => 'orange', 'green' => 'pineapple'], Arrays::subarray(['red' => 'apple', 'blue' => 'orange', 'green' => 'pineapple'], ['blue', 'green']));
        self::assertSame(['blue' => 'orange', 'green' => 'pineapple'], Arrays::subarray(['red' => 'apple', 'blue' => 'orange', 'green' => 'pineapple'], ['blue', 'green', 'orange']));
    }
}
