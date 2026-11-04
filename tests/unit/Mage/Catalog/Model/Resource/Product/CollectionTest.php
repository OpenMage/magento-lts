<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Catalog\Model\Resource\Product;

use Mage_Catalog_Model_Product;
use Mage_Catalog_Model_Resource_Product_Collection as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use ReflectionMethod;

final class CollectionTest extends OpenMageTest
{
    public function testPrepareUrlDataObjectHonorsExplicitlyFalseFlag(): void
    {
        $collection = $this->getMockBuilder(Subject::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFlag'])
            ->getMock();
        $collection->setFlag('url_data_object', false);
        $collection->expects(self::exactly(2))
            ->method('getFlag')
            ->willReturnMap([
                ['do_not_use_category_id', null],
                ['url_data_object', false],
            ]);

        $product = $this->createPartialMock(Mage_Catalog_Model_Product::class, ['getId', 'isVisibleInSiteVisibility']);
        $product->method('getId')->willReturn(42);
        $product->method('isVisibleInSiteVisibility')->willReturn(false);
        $product->setEntityId(42)->setItemStoreId(1);
        $collection->setItemObjectClass($product::class);
        $collection->addItem($product);

        $method = new ReflectionMethod(Subject::class, '_prepareUrlDataObject');

        self::assertSame($collection, $method->invoke($collection));
    }
}
