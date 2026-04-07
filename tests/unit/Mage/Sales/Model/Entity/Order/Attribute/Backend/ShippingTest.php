<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Sales\Model\Entity\Order\Attribute\Backend;

# use Mage;
# use Mage_Sales_Model_Entity_Order_Attribute_Backend_Shipping as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Sales\Model\Entity\Order\Attribute\Backend\ShippingTrait;

final class ShippingTest extends OpenMageTest
{
    use ShippingTrait;

    # private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        # self::$subject = Mage::getModel('sales/entity_order_attribute_backend_shipping');
        self::markTestSkipped('');
    }
}
