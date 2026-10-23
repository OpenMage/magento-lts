<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Sales\Model\Order\Creditmemo\Total;

// use Mage;
// use Mage_Sales_Model_Order_Creditmemo_Total_Shipping as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Sales\Model\Order\Creditmemo\Total\ShippingTrait;

final class ShippingTest extends OpenMageTest
{
    use ShippingTrait;

    // private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('sales/order_creditmemo_total_shipping');
        self::markTestSkipped('');
    }
}
