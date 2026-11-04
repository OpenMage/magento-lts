<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Sales\Model\Order\Creditmemo\Total;

use Mage;
use Mage_Sales_Model_Order_Creditmemo;
use Mage_Sales_Model_Order_Creditmemo_Total_Discount as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Sales\Model\Order\Creditmemo\Total\DiscountTrait;

final class DiscountTest extends OpenMageTest
{
    use DiscountTrait;

    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('sales/order_creditmemo_total_discount');
    }

    /**
     * @dataProvider provideCollectShippingDiscountData
     * @group Model
     */
    public function testCollectShippingDiscount(float $expected, float $orderShipping, float $orderShippingDiscount, float $refundShipping): void
    {
        $order = Mage::getModel('sales/order');
        $order->setShippingAmount($orderShipping)
            ->setBaseShippingAmount($orderShipping)
            ->setShippingDiscountAmount($orderShippingDiscount)
            ->setBaseShippingDiscountAmount($orderShippingDiscount);

        $creditmemo = $this->createPartialMock(Mage_Sales_Model_Order_Creditmemo::class, ['getAllItems']);
        $creditmemo->method('getAllItems')->willReturn([]);
        $creditmemo->setOrder($order);
        $creditmemo->setBaseShippingAmount($refundShipping);

        self::$subject->collect($creditmemo);

        self::assertSame($expected, (float) $creditmemo->getBaseDiscountAmount());
        self::assertSame($expected, (float) $creditmemo->getDiscountAmount());
    }
}
