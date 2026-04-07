<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Sales\Model\Entity\Quote\Address\Attribute\Frontend;

# use Mage;
# use Mage_Sales_Model_Entity_Quote_Address_Attribute_Frontend_Discount as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Sales\Model\Entity\Quote\Address\Attribute\Frontend\DiscountTrait;

final class DiscountTest extends OpenMageTest
{
    use DiscountTrait;

    # private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        # self::$subject = Mage::getModel('sales/entity_quote_address_attribute_frontend_discount');
        self::markTestSkipped('');
    }
}
