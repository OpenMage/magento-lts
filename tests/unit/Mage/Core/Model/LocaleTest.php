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

namespace OpenMage\Tests\Unit\Mage\Core\Model;

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
        $this->assertEquals($expectedResult, $this->subject->getNumber($value));
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
