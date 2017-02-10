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

namespace Mage\Adminhtml\Test\Block\Sales\Order\View;

use Mage\Adminhtml\Test\Block\Sales\Order\AbstractItemsNewBlock;
use Mage\Catalog\Test\Fixture\ConfigurableProduct;
use Magento\Mtf\Client\Locator;
use Magento\Mtf\Fixture\InjectableFixture;

/**
 * Block for items ordered on order page.
 */
class Items extends AbstractItemsNewBlock
{
    /**
     * Invoice item price xpath selector.
     *
     * @var string
     */
    protected $priceSelector = '//div[@class="price-excl-tax"]//span[@class="price"]';

    /**
     * Item block class.
     *
     * @var string
     */
    protected $classItemBlock = 'Mage\Adminhtml\Test\Block\Sales\Order\View\Items\Product';

    /**
     * Returns the item price for the specified product.
     *
     * @param InjectableFixture $product
     * @return array|string
     */
    public function getPrice(InjectableFixture $product)
    {
        $productName = $product->getName();

        if ($product instanceof ConfigurableProduct) {
            // Find the price for the specific configurable product that was purchased
            $configurableAttributes = $product->getConfigurableAttributes();
            $productOptions = $product->getCheckoutData()['options']['configurable_options'];
            $checkoutOption = reset($productOptions);
            $attributeKey = $checkoutOption['title'];
            $optionKey = $checkoutOption['value'];
            $attributeValue = $configurableAttributes[$attributeKey]['label']['value'];
            $optionValue = $configurableAttributes[$attributeKey][$optionKey]['option_label']['value'];

            $productDisplay = $productName . ' SKU: ' . $product->getVariationSku($checkoutOption);
            $productDisplay .= ' ' . $attributeValue . ' ' . $optionValue;
        } else {
            $productDisplay = $productName . ' SKU: ' . $product->getSku();
        }
        $selector = '//tr[normalize-space(td)="' . $productDisplay . '"]' . $this->priceSelector;

        return $this->_rootElement->find($selector, Locator::SELECTOR_XPATH)->getText();
    }
}
