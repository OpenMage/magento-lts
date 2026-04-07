<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\CatalogInventory\Model\Stock\Item\Api;

# use Mage;
# use Mage_CatalogInventory_Model_Stock_Item_Api_V2 as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\CatalogInventory\Model\Stock\Item\Api\V2Trait;

final class V2Test extends OpenMageTest
{
    use V2Trait;

    # private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        # self::$subject = Mage::getModel('cataloginventory/stock_item_api_v2');
        self::markTestSkipped('');
    }
}
