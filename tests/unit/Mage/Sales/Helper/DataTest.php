<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace unit\Mage\Sales\Helper;

use Mage;
use Mage_Sales_Helper_Data as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class DataTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('sales/data');
    }

    /**
     * @covers Mage_Sales_Helper_Data::canSendNewOrderConfirmationEmail()
     * @group Helper
     */
    public function testCanSendNewOrderConfirmationEmail(): void
    {
        self::assertIsBool(self::$subject->canSendNewOrderConfirmationEmail());
    }

    /**
     * @covers Mage_Sales_Helper_Data::canSendNewOrderEmail()
     * @group Helper
     */
    public function testCanSendNewOrderEmail(): void
    {
        self::assertIsBool(self::$subject->canSendNewOrderEmail());
    }

    /**
     * @covers Mage_Sales_Helper_Data::canSendOrderCommentEmail()
     * @group Helper
     */
    public function testCanSendOrderCommentEmail(): void
    {
        self::assertIsBool(self::$subject->canSendOrderCommentEmail());
    }

    /**
     * @covers Mage_Sales_Helper_Data::canSendNewShipmentEmail()
     * @group Helper
     */
    public function testCanSendNewShipmentEmail(): void
    {
        self::assertIsBool(self::$subject->canSendNewShipmentEmail());
    }

    /**
     * @covers Mage_Sales_Helper_Data::canSendShipmentCommentEmail()
     * @group Helper
     */
    public function testCanSendShipmentCommentEmail(): void
    {
        self::assertIsBool(self::$subject->canSendShipmentCommentEmail());
    }

    /**
     * @covers Mage_Sales_Helper_Data::canSendNewInvoiceEmail()
     * @group Helper
     */
    public function testCanSendNewInvoiceEmail(): void
    {
        self::assertIsBool(self::$subject->canSendNewInvoiceEmail());
    }

    /**
     * @covers Mage_Sales_Helper_Data::canSendInvoiceCommentEmail()
     * @group Helper
     */
    public function testCanSendInvoiceCommentEmail(): void
    {
        self::assertIsBool(self::$subject->canSendInvoiceCommentEmail());
    }

    /**
     * @covers Mage_Sales_Helper_Data::canSendNewCreditmemoEmail()
     * @group Helper
     */
    public function testCanSendNewCreditmemoEmail(): void
    {
        self::assertIsBool(self::$subject->canSendNewCreditmemoEmail());
    }

    /**
     * @covers Mage_Sales_Helper_Data::canSendCreditmemoCommentEmail()
     * @group Helper
     */
    public function testCanSendCreditmemoCommentEmail(): void
    {
        self::assertIsBool(self::$subject->canSendCreditmemoCommentEmail());
    }

    /**
     * @covers Mage_Sales_Helper_Data::getOldFieldMap()
     * @group Helper
     */
    public function testGetOldFieldMap(): void
    {
        self::assertIsArray(self::$subject->getOldFieldMap('invalid_string'));
    }
}
