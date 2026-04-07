<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\CatalogInventory\Model\Api2\Stock\Item\Rest\Admin;

# use Mage;
# use Mage_CatalogInventory_Model_Api2_Stock_Item_Rest_Admin_V1 as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\CatalogInventory\Model\Api2\Stock\Item\Rest\Admin\V1Trait;

final class V1Test extends OpenMageTest
{
    use V1Trait;

    # private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        # self::$subject = Mage::getModel('cataloginventory/api2_stock_item_rest_admin_v1');
        self::markTestSkipped('');
    }
}
