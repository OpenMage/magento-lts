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

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block\Customer\Edit\Tab;

use Mage;
use Mage_Adminhtml_Block_Customer_Edit_Tab_Addresses;
use Mage_Core_Model_Layout;
use Mage_Customer_Model_Customer;
use PHPUnit\Framework\TestCase;

class AddressesTest extends TestCase
{
    private static ?Mage_Adminhtml_Block_Customer_Edit_Tab_Addresses $subject;

    public static function setUpBeforeClass(): void
    {
        Mage::app();
        Mage::register('current_customer', new Mage_Customer_Model_Customer());
        self::$subject = new Mage_Adminhtml_Block_Customer_Edit_Tab_Addresses();
    }

    public static function tearDownAfterClass(): void
    {
        self::$subject = null;
    }

    /**
     * @covers Mage_Adminhtml_Block_Customer_Edit_Tab_Addresses::getButtonAddBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Customer
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonAddBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonAddBlock();
        $this->assertSame('add_address_button', $result->getId());
        $this->assertSame('Add New Address', $result->getLabel());
        $this->assertSame('add_address_button', $result->getName());
        $this->assertSame('add_address_button', $result->getElementName());
        $this->assertIsBool($result->getDisabled());
        $this->assertSame('customerAddresses.addNewAddress()', $result->getOnClick());
        $this->assertStringStartsWith('add', $result->getClass());
    }

    /**
     * @covers Mage_Adminhtml_Block_Customer_Edit_Tab_Addresses::getButtonCancelBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Customer
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonCancelBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonCancelBlock();
        $this->assertSame('cancel_add_address_template_', $result->getId());
        $this->assertSame('Cancel', $result->getLabel());
        $this->assertSame('cancel_address', $result->getName());
        $this->assertSame('cancel_address', $result->getElementName());
        $this->assertIsBool($result->getDisabled());
        $this->assertSame('customerAddresses.cancelAdd(this)', $result->getOnClick());
        $this->assertStringStartsWith('cancel delete-address', $result->getClass());
    }

    /**
     * @covers Mage_Adminhtml_Block_Customer_Edit_Tab_Addresses::getButtonSaveBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Customer
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonDeleteBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonDeleteBlock();
        $this->assertSame('Delete Address', $result->getLabel());
        $this->assertSame('delete_address', $result->getName());
        $this->assertSame('delete_address', $result->getElementName());
        $this->assertIsBool($result->getDisabled());
        $this->assertNull($result->getOnClick());
        $this->assertStringStartsWith('delete', $result->getClass());
    }

    /**
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     */
    public function testInitForm(): void
    {
        $mock = $this->getMockBuilder(Mage_Adminhtml_Block_Customer_Edit_Tab_Addresses::class)
            ->setMethods(['getRegistryCurrentCustomer', 'isReadonly'])
            ->getMock();

        $mock
            ->method('getRegistryCurrentCustomer')
            ->willReturn(new Mage_Customer_Model_Customer());

        $mock
            ->method('isReadonly')
            ->willReturn(true);

        $this->assertInstanceOf(Mage_Adminhtml_Block_Customer_Edit_Tab_Addresses::class, $mock->initForm());
    }
}
