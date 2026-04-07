<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Sales\Model\Resource\Order\Invoice\Attribute\Backend;

# use Mage;
# use Mage_Sales_Model_Resource_Order_Invoice_Attribute_Backend_Parent as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Sales\Model\Resource\Order\Invoice\Attribute\Backend\ParentTrait;

final class ParentTest extends OpenMageTest
{
    use ParentTrait;

    # private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        # self::$subject = Mage::getModel('sales/resource_order_invoice_attribute_backend_parent');
        self::markTestSkipped('');
    }
}
