<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Sales\Model\Quote\Address\Total\Nominal;

// use Mage;
// use Mage_Sales_Model_Quote_Address_Total_Nominal_Shipping as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Sales\Model\Quote\Address\Total\Nominal\ShippingTrait;

final class ShippingTest extends OpenMageTest
{
    use ShippingTrait;

    // private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('sales/quote_address_total_nominal_shipping');
        self::markTestSkipped('');
    }
}
