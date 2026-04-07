<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Sales\Model\Entity\Quote\Address\Attribute;

# use Mage;
use Mage_Sales_Model_Entity_Quote_Address_Attribute_Backend as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Sales\Model\Entity\Quote\Address\Attribute\BackendTrait;

final class BackendTest extends OpenMageTest
{
    use BackendTrait;

    # private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        # self::$subject = Mage::getModel('sales/entity_quote_address_attribute_backend');
        self::markTestSkipped('');
    }
}
