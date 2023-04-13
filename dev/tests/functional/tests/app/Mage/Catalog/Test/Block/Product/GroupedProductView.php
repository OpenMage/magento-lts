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
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Catalog\Test\Block\Product;

use Mage\Catalog\Test\Block\Product\View\GroupedProduct;
use Magento\Mtf\Fixture\InjectableFixture;

/**
 * Grouped product view block on frontend page.
 */
class GroupedProductView extends View
{
    /**
     * Block grouped product selector.
     *
     * @var string
     */
    protected $groupedProductBlock = '#super-product-table';

    /**
     * Special price block selector.
     *
     * @var string
     */
    protected $formatSpecialPrice = '//tr[%row-number%]//*[contains(@class,"price-box")]';

    /**
     * Tier price block selector.
     *
     * @var string
     */
    protected $formatTierPrice = "//tr[%row-number%]//li[%d]";

    /**
     * Get grouped product block.
     *
     * @return GroupedProduct
     */
    public function getGroupedProductBlock()
    {
        return $this->blockFactory->create(
            'Mage\Catalog\Test\Block\Product\View\GroupedProduct',
            ['element' => $this->_rootElement->find($this->groupedProductBlock)]
        );
    }

    /**
     * Return product options.
     *
     * @param InjectableFixture $product [optional]
     * @return array
     */
    public function getOptions(InjectableFixture $product = null)
    {
        return $this->getGroupedProductBlock()->getOptions($product);
    }

    /**
     * Get text of Stock Availability control.
     *
     * @return string
     */
    public function getGroupedStockAvailability()
    {
        return strtolower($this->_rootElement->find($this->stockAvailability)->getText());
    }

    /**
     * Change price selector.
     *
     * @param int $index
     * @return void
     */
    public function itemPriceProductBlock($index)
    {
        $this->priceBlock = str_replace('%row-number%', $index, $this->formatSpecialPrice);
    }

    /**
     * Change tier price selector.
     *
     * @param int $index
     * @return void
     */
    public function itemTierPriceProductBlock($index)
    {
        $this->tierPricesSelector = str_replace('%row-number%', $index, $this->formatTierPrice);
    }

    /**
     * Fill grouped product options.
     *
     * @param InjectableFixture $product
     * @return void
     */
    public function fillOptions(InjectableFixture $product)
    {
        $checkoutData = $checkoutData = $product->getCheckoutData();
        $associatedProducts = $product->getAssociated();
        if (isset($checkoutData['options'])) {
            $groupedBlock = $this->getGroupedProductBlock();
            foreach ($checkoutData['options'] as $key => $option) {
                $groupedBlock->getGroupedItemForm($associatedProducts[$key]['name'])->fillOption($option);
            }
        }
    }
}
