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

namespace OpenMage\Tests\Unit\Mage\Sales\Block\Order\Shipment\Create;

use Mage;
use Mage_Adminhtml_Block_Sales_Order_Shipment_Create_Items;
use Mage_Core_Model_Layout;
use PHPUnit\Framework\TestCase;

class ItemsTest extends TestCase
{
    private static ?Mage_Adminhtml_Block_Sales_Order_Shipment_Create_Items $subject;

    public static function setUpBeforeClass(): void
    {
        Mage::app();
        self::$subject = new Mage_Adminhtml_Block_Sales_Order_Shipment_Create_Items();
    }

    public static function tearDownAfterClass(): void
    {
        self::$subject = null;
    }

    /**
     * @covers Mage_Adminhtml_Block_Sales_Order_Shipment_Create_Items::getButtonSubmitBlock()
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group Mage_Adminhtml_Block_Sales
     * @group AdminhtmlButtons
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetButtonSubmitBlock(): void
    {
        self::$subject->setLayout(new Mage_Core_Model_Layout());

        $result = self::$subject->getButtonSubmitBlock();
        $this->assertSame('Submit Shipment', $result->getLabel());
        $this->assertSame('submitShipment(this);', $result->getOnClick());
        $this->assertSame('save submit-button', $result->getClass());
    }
}
