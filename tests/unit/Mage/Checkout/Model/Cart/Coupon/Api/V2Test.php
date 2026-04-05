<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Checkout\Model\Cart\Coupon\Api;

use Mage;
use Mage_Checkout_Model_Cart_Coupon_Api_V2 as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class V2Test extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('checkout/cart_coupon_api_v2');
    }
}
