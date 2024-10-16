<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Varien\Data\Form\Filter;

use Generator;
use PHPUnit\Framework\TestCase;
use Throwable;
use Varien_Data_Form_Filter_Date;

class DateTest extends TestCase
{
    public Varien_Data_Form_Filter_Date $subject;

    public function setUp(): void
    {
        $this->subject = new Varien_Data_Form_Filter_Date(null, 'en_US');
    }

    /**
     * @dataProvider provideFilterDateData
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

    public function provideFilterDateData(): Generator
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
            '1990-05-18',
            '1990-05-18',
        ];
        yield 'YYMMDD' => [
            '0090-05-18',
            '90-05-18',
        ];
        yield 'YYYYMD' => [
            '1990-05-08',
            '1990-5-8',
        ];
    }
}
