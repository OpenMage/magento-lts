<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Sales\Model\Resource\Quote\Address\Attribute\Frontend;

use Mage;
use Mage_Sales_Model_Resource_Quote_Address_Attribute_Frontend_Tax as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Sales\Model\Resource\Quote\Address\Attribute\Frontend\TaxTrait;

final class TaxTest extends OpenMageTest
{
    use TaxTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('sales/resource_quote_address_attribute_frontend_tax');
        self::markTestSkipped('');
    }
}
