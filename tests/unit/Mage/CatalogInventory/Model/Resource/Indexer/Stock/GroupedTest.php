<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\CatalogInventory\Model\Resource\Indexer\Stock;

// use Mage;
// use Mage_CatalogInventory_Model_Resource_Indexer_Stock_Grouped as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\CatalogInventory\Model\Resource\Indexer\Stock\GroupedTrait;

final class GroupedTest extends OpenMageTest
{
    use GroupedTrait;

    // private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('cataloginventory/resource_indexer_stock_grouped');
        self::markTestSkipped('');
    }
}
