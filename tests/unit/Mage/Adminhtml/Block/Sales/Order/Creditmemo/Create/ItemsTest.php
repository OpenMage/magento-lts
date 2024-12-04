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

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block\Sales\Order\Creditmemo\Create;

use Mage;
use Mage_Adminhtml_Block_Sales_Order_Creditmemo_Create_Items;
use Mage_Core_Model_Layout;
use Mage_Sales_Model_Order_Creditmemo;
use PHPUnit\Framework\TestCase;

class ItemsTest extends TestCase
{
    private static ?Mage_Adminhtml_Block_Sales_Order_Creditmemo_Create_Items $subject;

    public static function setUpBeforeClass(): void
    {
        Mage::app();
        Mage::register('current_creditmemo', new Mage_Sales_Model_Order_Creditmemo());

        self::$subject = new Mage_Adminhtml_Block_Sales_Order_Creditmemo_Create_Items();
    }

    public static function tearDownAfterClass(): void
    {
        self::$subject = null;
    }

    /**
     * @covers Mage_Adminhtml_Block_Sales_Order_Creditmemo_Create_Items::getButtonUpdateBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Sales
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
//    public function testGetButtonUpdateBlock(): void
//    {
//        self::$subject->setLayout(new Mage_Core_Model_Layout());
//
//        $result = self::$subject->getButtonUpdateBlock();
//        $this->assertSame('Update Qty\'s', $result->getLabel());
//        $this->assertStringStartsWith("submitAndReloadArea($('creditmemo_item_container'),'", $result->getOnClick());
//        $this->assertSame('update-button', $result->getClass());
//    }

    /**
     * @covers Mage_Adminhtml_Block_Sales_Order_Creditmemo_Create_Items::getButtonRefundBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Sales
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
//    public function testGetButtonRefundBlock(): void
//    {
//        self::$subject->setLayout(new Mage_Core_Model_Layout());
//
//        $result = self::$subject->getButtonRefundBlock();
//        $this->assertSame('Update Qty\'s', $result->getLabel());
//        $this->assertStringStartsWith("submitAndReloadArea($('creditmemo_item_container'),'", $result->getOnClick());
//        $this->assertSame('update-button', $result->getClass());
//    }

    /**
     * @covers Mage_Adminhtml_Block_Sales_Order_Creditmemo_Create_Items::getButtonRefundOfflineBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Sales
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
//    public function getButtonRefundOfflineBlock(): void
//    {
//        self::$subject->setLayout(new Mage_Core_Model_Layout());
//
//        $result = self::$subject->getButtonRefundOfflineBlock();
//        $this->assertSame('Update Qty\'s', $result->getLabel());
//        $this->assertStringStartsWith("submitAndReloadArea($('creditmemo_item_container'),'", $result->getOnClick());
//        $this->assertSame('update-button', $result->getClass());
//    }
}
