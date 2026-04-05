<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Sales\Model\Entity\Quote\Address\Attribute\Backend;

use Mage;
use Mage_Sales_Model_Entity_Quote_Address_Attribute_Backend_Child as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class ChildTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('sales/entity_quote_address_attribute_backend_child');
    }
}
