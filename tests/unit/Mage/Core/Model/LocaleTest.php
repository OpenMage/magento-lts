<?php

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model;

use Mage_Core_Model_Locale;
use PHPUnit\Framework\TestCase;

class LocaleTest extends TestCase
{
    /**
     * @var Mage_Core_Model_Locale
     */
    public Mage_Core_Model_Locale $subject;

    /**
     * @dataProvider provideGetNumberData
     * @param float|null $expectedResult
     * @param string|float|int $value
     * @return void
     */
    public function testGetNumber(?float $expectedResult, $value): void
    {
        // phpcs:ignore Ecg.Classes.ObjectInstantiation.DirectInstantiation
        $this->subject = new Mage_Core_Model_Locale();
        $this->assertSame($expectedResult, $this->subject->getNumber($value));
    }

    /**
     * @return array<string, array<int, array<int, int>|float|int|string|null>>
     */
    public function provideGetNumberData(): array
    {
        return [
            'array' => [
                1.0,
                [1]
            ],
            'int' => [
                1.0,
                1
            ],
            'string' => [
                1.0,
                '1'
            ],
            'string_comma' => [
                1.0,
                '1,0'
            ],
            'string_dot' => [
                1.0,
                '1.0'
            ],
            'null' => [
                null,
                null
            ],
        ];
    }
}
