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
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
