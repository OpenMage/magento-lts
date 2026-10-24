<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\CatalogInventory\Model;

use Mage;
use Mage_Catalog_Model_Resource_Product_Collection;
use Mage_CatalogInventory_Model_Observer as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use Varien_Event;
use Varien_Event_Observer;

final class ObserverTest extends OpenMageTest
{
    public function testAddStockStatusToCollectionHonorsExplicitlyFalseFlags(): void
    {
        $collection = $this->getMockBuilder(Mage_Catalog_Model_Resource_Product_Collection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFlag', 'load'])
            ->getMock();
        $collection->method('load')->willReturnSelf();
        $collection->setFlag('no_stock_data', false);
        $collection->setFlag('require_stock_items', false);
        $collection->expects(self::exactly(2))
            ->method('getFlag')
            ->willReturnMap([
                ['no_stock_data', false],
                ['require_stock_items', false],
            ]);

        $observer = new Varien_Event_Observer([
            'event' => new Varien_Event(['collection' => $collection]),
        ]);
        /** @var Subject $subject */
        $subject = Mage::getModel('cataloginventory/observer');

        self::assertSame($subject, $subject->addStockStatusToCollection($observer));
    }
}
