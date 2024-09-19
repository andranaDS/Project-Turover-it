<?php

namespace App\Sync\Tests\Unit\Transformer\JobPosting;

use App\Sync\Transformer\JobPosting\SalaryTransformer;
use PHPUnit\Framework\TestCase;

class SalaryTransformerTest extends TestCase
{
    public function testTransform(): void
    {
        self::assertSame([
            'minAnnualSalary' => 95000,
            'maxAnnualSalary' => 110000,
        ], SalaryTransformer::transform('95 à 110'));

        self::assertNull(SalaryTransformer::transform('Eur10 - Eur2000 jour + compétitif'));

        self::assertSame([
            'minAnnualSalary' => 45000,
            'maxAnnualSalary' => 110000,
        ], SalaryTransformer::transform('45 à 110'));

        self::assertNull(SalaryTransformer::transform('2200 - 2300 Mensuel'));
        self::assertNull(SalaryTransformer::transform('3'));

        self::assertSame([
            'minAnnualSalary' => 40000,
            'maxAnnualSalary' => 44000,
        ], SalaryTransformer::transform('40-44k€/an OU 300-320€/j'));

        self::assertNull(SalaryTransformer::transform('3'));

        self::assertSame([
            'minAnnualSalary' => 50000,
            'maxAnnualSalary' => 60000,
        ], SalaryTransformer::transform('50-60K dont 2K de variable'));

        self::assertSame([
            'minAnnualSalary' => 40000,
            'maxAnnualSalary' => 55000,
        ], SalaryTransformer::transform('40-55K€ ou 550 TJM'));

        self::assertNull(SalaryTransformer::transform('200001'));

        self::assertSame([
            'minAnnualSalary' => 150000,
            'maxAnnualSalary' => 200000,
        ], SalaryTransformer::transform('150000-200000'));

        self::assertSame([
            'minAnnualSalary' => 10000,
            'maxAnnualSalary' => 10000,
        ], SalaryTransformer::transform('10000'));

        self::assertNull(SalaryTransformer::transform('9999'));

        self::assertNull(SalaryTransformer::transform('2001'));

        self::assertSame([
            'minDailySalary' => 1000,
            'maxDailySalary' => 2000,
        ], SalaryTransformer::transform('1000-2000'));

        self::assertSame([
            'minDailySalary' => 2000,
            'maxDailySalary' => 2000,
        ], SalaryTransformer::transform('2000'));

        self::assertSame([
            'minDailySalary' => 150,
            'maxDailySalary' => 150,
        ], SalaryTransformer::transform('150'));

        self::assertNull(SalaryTransformer::transform('00'));

        self::assertNull(SalaryTransformer::transform('0'));

        self::assertSame([
            'minDailySalary' => 500,
            'maxDailySalary' => 500,
        ], SalaryTransformer::transform('500.0'));

        self::assertSame([
            'minAnnualSalary' => 35000,
            'maxAnnualSalary' => 35000,
        ], SalaryTransformer::transform('A partir de 35'));

        self::assertSame([
            'minAnnualSalary' => 35000,
            'maxAnnualSalary' => 45000,
        ], SalaryTransformer::transform('Entre 35 et 45k'));

        self::assertSame([
            'minAnnualSalary' => 35000,
            'maxAnnualSalary' => 45000,
        ], SalaryTransformer::transform('35-45k'));

        self::assertSame([
            'minAnnualSalary' => 35000,
            'maxAnnualSalary' => 45000,
        ], SalaryTransformer::transform('35 à 45k'));

        self::assertSame([
            'minAnnualSalary' => 60000,
            'maxAnnualSalary' => 80000,
        ], SalaryTransformer::transform('de 80k à 60k'));

        self::assertSame([
            'minAnnualSalary' => 60000,
            'maxAnnualSalary' => 80000,
        ], SalaryTransformer::transform('de 60k à 80k'));

        self::assertSame([
            'minAnnualSalary' => 55000,
            'maxAnnualSalary' => 55000,
        ], SalaryTransformer::transform('55K euros par an'));

        self::assertSame([
            'minAnnualSalary' => 55000,
            'maxAnnualSalary' => 55000,
        ], SalaryTransformer::transform('55k euros'));

        self::assertSame([
            'minAnnualSalary' => 55000,
            'maxAnnualSalary' => 55000,
        ], SalaryTransformer::transform('55 K€'));

        self::assertSame([
            'minAnnualSalary' => 55000,
            'maxAnnualSalary' => 55000,
        ], SalaryTransformer::transform('55K€'));

        self::assertSame([
            'minAnnualSalary' => 55000,
            'maxAnnualSalary' => 55000,
        ], SalaryTransformer::transform('55 k€'));

        self::assertSame([
            'minAnnualSalary' => 55000,
            'maxAnnualSalary' => 55000,
        ], SalaryTransformer::transform('55k€'));

        self::assertSame([
            'minAnnualSalary' => 55000,
            'maxAnnualSalary' => 55000,
        ], SalaryTransformer::transform('55k par an'));

        self::assertSame([
            'minAnnualSalary' => 55000,
            'maxAnnualSalary' => 55000,
        ], SalaryTransformer::transform('55k'));

        self::assertSame([
            'minAnnualSalary' => 45000,
            'maxAnnualSalary' => 55000,
        ], SalaryTransformer::transform('55000-45000€'));

        self::assertSame([
            'minAnnualSalary' => 35000,
            'maxAnnualSalary' => 45000,
        ], SalaryTransformer::transform('Eur35000 - Eur45000 per annum'));

        self::assertSame([
            'minAnnualSalary' => 45000,
            'maxAnnualSalary' => 55000,
        ], SalaryTransformer::transform('55000-45000'));

        self::assertSame([
            'minAnnualSalary' => 45000,
            'maxAnnualSalary' => 55000,
        ], SalaryTransformer::transform('45000-55000'));

        self::assertSame([
            'minAnnualSalary' => 45000,
            'maxAnnualSalary' => 45000,
        ], SalaryTransformer::transform('45000'));

        self::assertSame([
            'minDailySalary' => 350,
            'maxDailySalary' => 500,
        ], SalaryTransformer::transform('Eur350 - Eur500 jour'));

        self::assertNull(SalaryTransformer::transform('à définir'));

        self::assertNull(SalaryTransformer::transform('Selon experience'));

        self::assertNull(SalaryTransformer::transform('Selon profil'));

        self::assertSame([
            'minDailySalary' => 450,
            'maxDailySalary' => 450,
        ], SalaryTransformer::transform('450 Euro/jour'));

        self::assertSame([
            'minDailySalary' => 500,
            'maxDailySalary' => 600,
        ], SalaryTransformer::transform('de 500€ à 600€'));

        self::assertSame([
            'minDailySalary' => 500,
            'maxDailySalary' => 600,
        ], SalaryTransformer::transform('600-500'));

        self::assertSame([
            'minDailySalary' => 500,
            'maxDailySalary' => 600,
        ], SalaryTransformer::transform('500-600'));

        self::assertSame([
            'minDailySalary' => 500,
            'maxDailySalary' => 500,
        ], SalaryTransformer::transform('500'));
    }
}
