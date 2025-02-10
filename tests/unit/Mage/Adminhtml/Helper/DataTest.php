<?php

/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @covers Mage_Adminhtml_Helper_Data::getUrl()
 * @group Mage_Adminhtml
 * @group Mage_Adminhtml_Helper
 * @group runInSeparateProcess
 */
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Helper;

use Mage;
use Mage_Adminhtml_Helper_Data as Subject;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('adminhtml/data');
    }


    public function testGetUrl(): void
    {
        $this->assertIsString($this->subject->getUrl());
    }

    /**
     * @covers Mage_Adminhtml_Helper_Data::getCurrentUserId()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Helper
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetCurrentUserId(): void
    {
        $this->assertFalse($this->subject->getCurrentUserId());
    }

    /**
     * @covers Mage_Adminhtml_Helper_Data::prepareFilterString()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Helper
     */
    public function testPrepareFilterString(): void
    {
        $this->assertIsArray($this->subject->prepareFilterString(''));
    }

    /**
     * @covers Mage_Adminhtml_Helper_Data::decodeFilter()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Helper
     */
    public function testDecodeFilter(): void
    {
        $string = '';
        $this->subject->decodeFilter($string);
        $this->assertSame('', $string);
    }

    /**
     * @covers Mage_Adminhtml_Helper_Data::isEnabledSecurityKeyUrl()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Helper
     */
    public function testIsEnabledSecurityKeyUrl(): void
    {
        $this->assertTrue($this->subject->isEnabledSecurityKeyUrl());
    }
}
