<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Tax\Model\Resource\Sales\Order\Tax\Item;

use Mage;
use Mage_Tax_Model_Resource_Sales_Order_Tax_Item_Collection as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class CollectionTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('tax/resource_sales_order_tax_item_collection');
    }
}
