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

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block\Sales\View;

use Mage;
use Mage_Adminhtml_Block_Sales_Order_View_History;
use Mage_Core_Model_Layout;
use Mage_Sales_Model_Order;
use PHPUnit\Framework\TestCase;

class HistoryTest extends TestCase
{
    private static ?Mage_Adminhtml_Block_Sales_Order_View_History $subject;

    public static function setUpBeforeClass(): void
    {
        Mage::app();
        Mage::register('sales_order', new Mage_Sales_Model_Order());
        self::$subject = new Mage_Adminhtml_Block_Sales_Order_View_History();
    }

    public static function tearDownAfterClass(): void
    {
        self::$subject = null;
    }

    /**
     * @covers Mage_Adminhtml_Block_Sales_Order_View_History::getButtonSaveBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Sales
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonSaveBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonSaveBlock();
        $this->assertSame('Submit Comment', $result->getLabel());
        $this->assertStringStartsWith("submitAndReloadArea($('order_history_block').parentNode", $result->getOnClick());
        $this->assertSame('save', $result->getClass());
    }
}
