<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block\Customer\Edit\Tab\View\Grid\Renderer;

use Mage_Adminhtml_Block_Customer_Edit_Tab_View_Grid_Renderer_Item as Subject;
use Mage_Catalog_Model_Product;
use Mage_Catalog_Model_Product_Type_Configurable;
use Mage_Downloadable_Model_Product_Type;
use Mage_Wishlist_Model_Item;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Adminhtml\Block\Customer\Edit\Tab\View\Grid\Renderer\ItemTrait;
use ReflectionMethod;
use Varien_Object;

/**
 * The customer cart and wishlist grids print option values as HTML, so the product configuration
 * helpers must escape admin-entered option labels and link titles.
 */
final class ItemTest extends OpenMageTest
{
    use ItemTrait;

    /**
     * @covers Mage_Adminhtml_Block_Customer_Edit_Tab_View_Grid_Renderer_Item::getOptionList()
     * @dataProvider provideGetOptionListData
     * @group Block
     * @param list<array<string, mixed>>               $expectedResult
     * @param list<array<string, string>>|list<string> $stored         attribute options or link titles
     */
    public function testGetOptionList(array $expectedResult, string $productType, array $stored): void
    {
        $product = $this->createPartialMock(Mage_Catalog_Model_Product::class, ['getTypeInstance']);
        $product->setTypeId($productType);

        $item = $this->createPartialMock(Mage_Wishlist_Model_Item::class, ['getProduct', 'getOptionByCode']);
        $item->method('getProduct')->willReturn($product);

        if ($productType === Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE) {
            $typeInstance = $this->createMock(Mage_Catalog_Model_Product_Type_Configurable::class);
            $typeInstance->method('getSelectedAttributesInfo')->willReturn($stored);
            $item->method('getOptionByCode')->willReturn(null);
        } else {
            $product->setLinksTitle('Links');
            $links = [];
            foreach ($stored as $id => $title) {
                $links[$id + 1] = new Varien_Object(['title' => $title]);
            }

            $typeInstance = $this->createMock(Mage_Downloadable_Model_Product_Type::class);
            $typeInstance->method('getLinks')->willReturn($links);
            $item->method('getOptionByCode')->willReturnCallback(
                static fn(string $code) => $code === 'downloadable_link_ids'
                    ? new Varien_Object(['value' => implode(',', array_keys($links))])
                    : null,
            );
        }

        $product->method('getTypeInstance')->willReturn($typeInstance);

        $subject = new Subject();
        $subject->setProductHelpers([
            Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE => 'downloadable/catalog_product_configuration',
        ]);
        $subject->setItem($item);

        $method = new ReflectionMethod($subject, 'getOptionList');
        self::assertSame($expectedResult, $method->invoke($subject));
    }
}
