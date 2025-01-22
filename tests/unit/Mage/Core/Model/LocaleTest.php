<?php

/**
 * @category   OpenMage
 * @package    OpenMage_Tests
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model;

use Generator;
use Mage;
use Mage_Core_Model_Locale;
use PHPUnit\Framework\TestCase;

class LocaleTest extends TestCase
{
    public Mage_Core_Model_Locale $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('core/locale');
    }

    /**
     * @dataProvider provideGetNumberData
     * @param string|float|int $value
     *
     * @group Mage_Core
     */
    public function testGetNumber(?float $expectedResult, $value): void
    {
        $this->assertSame($expectedResult, $this->subject->getNumber($value));
    }

    public function provideGetNumberData(): Generator
    {
        yield 'array' => [
            1.0,
            [1],
        ];
        yield 'int' => [
            1.0,
            1,
        ];
        yield 'string' => [
            1.0,
            '1',
        ];
        yield 'string comma' => [
            1.0,
            '1,0',
        ];
        yield 'string dot' => [
            1.0,
            '1.0',
        ];
        yield 'null' => [
            null,
            null,
        ];
    }
}
