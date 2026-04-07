<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Sales\Model\Entity\Order\Creditmemo\Item;

// use Mage;
// use Mage_Sales_Model_Entity_Order_Creditmemo_Item_Collection as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Sales\Model\Entity\Order\Creditmemo\Item\CollectionTrait;

final class CollectionTest extends OpenMageTest
{
    use CollectionTrait;

    // private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('sales/entity_order_creditmemo_item_collection');
        self::markTestSkipped('');
    }
}
