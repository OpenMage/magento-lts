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

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block\Sales\Order\Create\Sidebar;

use Mage;
use Mage_Adminhtml_Block_Sales_Order_Create_Sidebar_Cart;
use Mage_Core_Model_Layout;
use PHPUnit\Framework\TestCase;

class CartTest extends TestCase
{
    private static ?Mage_Adminhtml_Block_Sales_Order_Create_Sidebar_Cart $subject;

    public static function setUpBeforeClass(): void
    {
        Mage::app();
        self::$subject = new Mage_Adminhtml_Block_Sales_Order_Create_Sidebar_Cart();
    }

    public static function tearDownAfterClass(): void
    {
        self::$subject = null;
    }

    /**
     * @covers Mage_Adminhtml_Block_Sales_Order_Create_Sidebar_Cart::getButtonEmptyCustomerCartBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Sales
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonEmptyCustomerCartBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonEmptyCustomerCartBlock();
        $this->assertSame('Clear Shopping Cart', $result->getLabel());
        $this->assertStringStartsWith('order.clearShoppingCart(', $result->getOnClick());
        $this->assertNull($result->getClass());
        $this->assertSame('float: right;', $result->getStyle());
    }
}
