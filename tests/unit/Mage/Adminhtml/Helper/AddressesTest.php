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
use Mage_Adminhtml_Helper_Addresses as Subject;
use Mage_Customer_Model_Attribute;
use PHPUnit\Framework\TestCase;

class AddressesTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('adminhtml/addresses');
    }

    /**
     * @covers Mage_Adminhtml_Helper_Addresses::processStreetAttribute()
     * @dataProvider provideProcessStreetAttribute
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Helper
     */
    public function testProcessStreetAttribute(int $expectedResult, int $lines): void
    {
        $attribute = new Mage_Customer_Model_Attribute();
        $attribute->setScopeMultilineCount($lines);

        $result = $this->subject->processStreetAttribute($attribute);
        $this->assertSame($expectedResult, $result->getScopeMultilineCount());
    }

    public function provideProcessStreetAttribute(): Generator
    {
        yield 'default' => [
            Subject::DEFAULT_STREET_LINES_COUNT,
            0,
        ];
        yield 'custom' => [
            4,
            4,
        ];
    }
}
