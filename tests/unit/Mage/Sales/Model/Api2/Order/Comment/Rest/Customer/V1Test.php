<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Sales\Model\Api2\Order\Comment\Rest\Customer;

# use Mage;
use Mage_Sales_Model_Api2_Order_Comment_Rest_Customer_V1 as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Sales\Model\Api2\Order\Comment\Rest\Customer\V1Trait;

final class V1Test extends OpenMageTest
{
    use V1Trait;

    # private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        # self::$subject = Mage::getModel('sales/api2_order_comment_rest_customer_v1');
        self::markTestSkipped('');
    }
}
