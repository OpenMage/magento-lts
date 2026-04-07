<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Catalog\Model\Resource\Product\Indexer\Price;

# use Mage;
# use Mage_Catalog_Model_Resource_Product_Indexer_Price_Grouped as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Catalog\Model\Resource\Product\Indexer\Price\GroupedTrait;

final class GroupedTest extends OpenMageTest
{
    use GroupedTrait;

    # private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        # self::$subject = Mage::getModel('catalog/resource_product_indexer_price_grouped');
        self::markTestSkipped('');
    }
}
