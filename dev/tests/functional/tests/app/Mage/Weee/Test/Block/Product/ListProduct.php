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

namespace Mage\Weee\Test\Block\Product;

use Mage\Weee\Test\Block\Product\ProductList\ProductItem;
use Magento\Mtf\Client\Locator;
use Magento\Mtf\Fixture\InjectableFixture;

/**
 * Product list block.
 */
class ListProduct extends \Mage\Catalog\Test\Block\Product\ListProduct
{
    /**
     * Locator for product item block.
     *
     * @var string
     */
    protected $productItem = './/li[contains(@class,"item") and .//a[text()="%s"]]';

    /**
     * Return product item block.
     *
     * @param InjectableFixture $product
     * @return ProductItem
     */
    public function getProductItem(InjectableFixture $product)
    {
        $locator = sprintf($this->productItem, $product->getName());
        return $this->blockFactory->create(
            'Mage\Weee\Test\Block\Product\ProductList\ProductItem',
            ['element' => $this->_rootElement->find($locator, Locator::SELECTOR_XPATH)]
        );
    }
}
