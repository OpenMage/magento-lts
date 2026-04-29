<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Checkout\Model\Cart\Payment\Api;

// use Mage;
// use Mage_Checkout_Model_Cart_Payment_Api_V2 as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Checkout\Model\Cart\Payment\Api\V2Trait;

final class V2Test extends OpenMageTest
{
    use V2Trait;

    // private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('checkout/cart_payment_api_v2');
        self::markTestSkipped('');
    }
}
