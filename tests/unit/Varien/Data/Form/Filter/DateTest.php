<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Varien\Data\Form\Filter;

use Generator;
use PHPUnit\Framework\TestCase;
use Throwable;
use Varien_Data_Form_Filter_Date;

final class DateTest extends TestCase
{
    public Varien_Data_Form_Filter_Date $subject;

    protected function setUp(): void
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
            self::assertSame($expectedResult, $this->subject->inputFilter($value));
        } catch (Throwable $throwable) {
            // PHP7: bcsub(): bcmath function argument is not well-formed
            // PHP8: bcsub(): Argument #1 ($num1) is not well-formed
            self::assertStringStartsWith((string) $expectedResult, $throwable->getMessage());
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
