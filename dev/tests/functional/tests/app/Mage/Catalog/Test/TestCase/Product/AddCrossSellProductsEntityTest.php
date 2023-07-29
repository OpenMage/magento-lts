<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Tests
 * @package    Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Catalog\Test\TestCase\Product;

/**
 * Preconditions:
 * 1. Create cross cell products.
 *
 * Steps:
 * 1. Open Backend.
 * 2. Go to Products -> Catalog.
 * 3. Click Add new product.
 * 4. Fill data from dataset.
 * 5. Save product.
 * 6. Perform all assertions.
 *
 * @group Cross-sells_(MX)
 * @ZephyrId MPERF-6832
 */
class AddCrossSellProductsEntityTest extends AbstractAddAppurtenantProductsEntityTest
{
    /**
     * Run test add cross sell products entity.
     *
     * @param string $productData
     * @param string $crossSellProductsData
     * @return array
     */
    public function test($productData, $crossSellProductsData)
    {
        $product = $this->getProductByData($productData, ['cross_sell_products' => $crossSellProductsData]);
        $this->createAndSaveProduct($product);

        return [
            'product' => $product,
            'crossSellProducts' => $product->getDataFieldConfig('cross_sell_products')['source']->getProducts()
        ];
    }
}
