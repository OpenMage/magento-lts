<?php

/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @covers Mage_Adminhtml_Helper_Addresses::processStreetAttribute()
 * @dataProvider provideProcessStreetAttribute
 * @group Mage_Adminhtml
 * @group Mage_Adminhtml_Helper
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
