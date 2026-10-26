<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Sales\Model\Entity\Order\Invoice\Attribute\Backend;

// use Mage;
// use Mage_Sales_Model_Entity_Order_Invoice_Attribute_Backend_Order as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Sales\Model\Entity\Order\Invoice\Attribute\Backend\OrderTrait;

final class OrderTest extends OpenMageTest
{
    use OrderTrait;

    // private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('sales/entity_order_invoice_attribute_backend_order');
        self::markTestSkipped('');
    }
}
