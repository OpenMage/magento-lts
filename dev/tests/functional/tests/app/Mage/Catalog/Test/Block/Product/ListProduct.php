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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Catalog\Test\Block\Product;

use Magento\Mtf\Block\Block;
use Magento\Mtf\Client\Element\SimpleElement as Element;
use Magento\Mtf\Client\Locator;
use Magento\Mtf\Fixture\InjectableFixture;

/**
 * Product list.
 */
class ListProduct extends Block
{
    /**
     * This member holds the class name of the regular price block.
     *
     * @var string
     */
    protected $regularPriceClass = ".regular-price";

    /**
     * This member holds the class name for the price block found inside the product details.
     *
     * @var string
     */
    protected $priceBlockClass = 'price-box';

    /**
     * This member contains the selector to find the product details for the named product.
     *
     * @var string
     */
    protected $productDetailsSelector = '//*[contains(@class, "product-info") and .//*[@title="%s"]]';

    /**
     * Product name.
     *
     * @var string
     */
    protected $productTitle = '.product-name [title="%s"]';

    /**
     * Minimum Advertised Price on category page.
     *
     * @var string
     */
    protected $oldPrice = ".old-price .price-container";

    /**
     * 'Add to Card' button.
     *
     * @var string
     */
    protected $addToCard = "button.action.tocart";

    /**
     * Price box CSS selector.
     *
     * @var string
     */
    protected $priceBox = '.price-box #product-price-%s .price';

    /**
     * Sorter dropdown selector.
     *
     * @var string
     */
    protected $sorter = '.sort-by select';

    /**
     * This method returns the price box block for the named product.
     *
     * @param string $productName String containing the name of the product to find.
     * @return Price
     */
    public function getProductPriceBlock($productName)
    {
        $productDetails = $this->getProductDetailsElement($productName);
        return $this->blockFactory->create(
            'Mage\Catalog\Test\Block\Product\Price',
            ['element' => $productDetails->find($this->priceBlockClass, Locator::SELECTOR_CLASS_NAME)]
        );
    }

    /**
     * Check if product with specified name is visible.
     *
     * @param InjectableFixture $product
     * @return bool
     */
    public function isProductVisible($product)
    {
        return $this->getProductNameElement($product->getName())->isVisible();
    }

    /**
     * Check if regular price is visible.
     *
     * @return bool
     */
    public function isRegularPriceVisible()
    {
        return $this->_rootElement->find($this->regularPriceClass)->isVisible();
    }

    /**
     * Open product view page by clicking on product name.
     *
     * @param string $productName
     * @return void
     */
    public function openProductViewPage($productName)
    {
        $this->getProductNameElement($productName)->click();
    }

    /**
     * This method returns the element representing the product details for the named product.
     *
     * @param string $productName String containing the name of the product
     * @return Element
     */
    protected function getProductDetailsElement($productName)
    {
        return $this->_rootElement->find(
            sprintf($this->productDetailsSelector, $productName),
            Locator::SELECTOR_XPATH
        );
    }

    /**
     * This method returns the element on the page associated with the product name.
     *
     * @param string $productName String containing the name of the product
     * @return Element
     */
    protected function getProductNameElement($productName)
    {
        return $this->_rootElement->find(sprintf($this->productTitle, $productName));
    }

    /**
     * Get Minimum Advertised Price on Category page.
     *
     * @return string
     */
    public function getOldPriceCategoryPage()
    {
        return $this->_rootElement->find($this->oldPrice, Locator::SELECTOR_CSS)->getText();
    }

    /**
     * Retrieve product price by specified Id.
     *
     * @param int $productId
     * @return string
     */
    public function getPrice($productId)
    {
        return $this->_rootElement->find(sprintf($this->priceBox, $productId), Locator::SELECTOR_CSS)->getText();
    }

    /**
     * Check 'Add To Card' button availability.
     *
     * @return bool
     */
    public function checkAddToCardButton()
    {
        return $this->_rootElement->find($this->addToCard, Locator::SELECTOR_CSS)->isVisible();
    }

    /**
     * Get all terms used in sort.
     *
     * @return array
     */
    public function getSortByValues()
    {
        return explode("\n", $this->_rootElement->find($this->sorter)->getText());
    }
}
