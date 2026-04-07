<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\CatalogInventory\Model\Api2\Stock\Item\Validator;

# use Mage;
use Mage_CatalogInventory_Model_Api2_Stock_Item_Validator_Item as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\CatalogInventory\Model\Api2\Stock\Item\Validator\ItemTrait;

final class ItemTest extends OpenMageTest
{
    use ItemTrait;

    # private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        # self::$subject = Mage::getModel('cataloginventory/api2_stock_item_validator_item');
        self::markTestSkipped('');
    }
}
