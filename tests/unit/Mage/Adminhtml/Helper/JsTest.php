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

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Helper;

use Generator;
use Mage;
use Mage_Adminhtml_Helper_Js as Subject;
use PHPUnit\Framework\TestCase;

class JsTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('adminhtml/js');
    }

    /**
     * @covers Mage_Adminhtml_Helper_Js::decodeGridSerializedInput()
     * @dataProvider provideDecodeGridSerializedInput
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Helper
     */
    public function testDecodeGridSerializedInput(array $expectedResult, string $encoded): void
    {
        $this->assertSame($expectedResult, $this->subject->decodeGridSerializedInput($encoded));
    }

    public function provideDecodeGridSerializedInput(): Generator
    {
        yield 'w/o keys' => [
            [
                0 => 1,
                1 => 2,
                2 => 3,
                3 => 4,
            ],
            '1&2&3&4',
        ];
        yield 'w/ keys' => [
            [
                1 => [],
                2 => [],
            ],
            '1=1&2=2',
        ];
    }
}
