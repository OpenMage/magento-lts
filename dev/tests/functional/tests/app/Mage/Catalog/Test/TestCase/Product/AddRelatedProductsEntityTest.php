<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
