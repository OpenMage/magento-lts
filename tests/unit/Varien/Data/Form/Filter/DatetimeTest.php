<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Varien\Data\Form\Filter;

use Generator;
use PHPUnit\Framework\TestCase;
use Throwable;
use Varien_Data_Form_Filter_Datetime;

class DatetimeTest extends TestCase
{
    public Varien_Data_Form_Filter_Datetime $subject;

    public function setUp(): void
    {
        $this->subject = new Varien_Data_Form_Filter_Datetime(null, 'en_US');
    }

    /**
     * @dataProvider provideFilterDatetimeData
     *
     * @group Varien_Data
     */
    public function testInputFilter(?string $expectedResult, ?string $value): void
    {
        try {
            $this->assertSame($expectedResult, $this->subject->inputFilter($value));
        } catch (Throwable $e) {
            // PHP7: bcsub(): bcmath function argument is not well-formed
            // PHP8: bcsub(): Argument #1 ($num1) is not well-formed
            $this->assertStringStartsWith((string) $expectedResult, $e->getMessage());
        }
    }

    public function provideFilterDatetimeData(): Generator
    {
        yield 'bcsub() exception' => [
            'bcsub():',
            '1990-18-18',
        ];
        yield 'null' => [
            null,
            null,
        ];
        yield 'empty' => [
            '',
            '',
        ];
        yield 'YYYYMMDD' => [
            '1990-05-18 00:00:00',
            '1990-05-18',
        ];
        yield 'YYMMDD' => [
            '0090-05-18 00:00:00',
            '90-05-18',
        ];
        yield 'YYYYMD' => [
            '1990-05-08 00:00:00',
            '1990-5-8',
        ];
    }
}
