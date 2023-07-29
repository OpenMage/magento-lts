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
 * 1. Create simple Product.
 * 2. Create Configurable Product.
 *
 * Steps:
 * 1. Open Backend.
 * 2. Go to Products -> Catalog.
 * 3. Press "Add product" button.
 * 4. Fill data according to dataset.
 * 5. Save product.
 * 6. Perform all assertions.
 *
 * @group Related_Products_(MX)
 * @ZephyrId MPERF-6822
 */
class AddRelatedProductsEntityTest extends AbstractAddAppurtenantProductsEntityTest
{
    /**
     * Run test add related products.
     *
     * @param string $productData
     * @param string $relatedProductsData
     * @return array
     */
    public function test($productData, $relatedProductsData)
    {
        $product = $this->getProductByData($productData, ['related_products' => $relatedProductsData]);
        $this->createAndSaveProduct($product);

        return [
            'product' => $product,
            'relatedProducts' => $product->getDataFieldConfig('related_products')['source']->getProducts()
        ];
    }
}
