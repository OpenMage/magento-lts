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
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Sales\Test\Block\Order\OrderPrint;

use Magento\Mtf\Block\Block;
use Magento\Mtf\Client\Locator;
use Magento\Mtf\Fixture\InjectableFixture;

/**
 * View block on order's print page.
 */
class View extends Block
{
    /**
     * Billing address locator.
     *
     * @var string
     */
    protected $billingAddress = '//div[contains(.,"Billing Address")]/address';

    /**
     * Grand total selector.
     *
     * @var string
     */
    protected $grandTotal = '.grand_total .price';

    /**
     * Payment method selector.
     *
     * @var string
     */
    protected $paymentMethod = '//div[h2[contains(.,"Payment Method")]]/p';

    /**
     * Shipping method selector.
     *
     * @var string
     */
    protected $shippingMethod = '//div[h2[contains(.,"Shipping Method")]]';

    /**
     * Item product selector.
     *
     * @var string
     */
    protected $itemProduct = '//h3[@class="product-name" and contains(.,"%s")]';

    /**
     * Get order billing address.
     *
     * @return string
     */
    public function getBillingAddress()
    {
        return $this->_rootElement->find($this->billingAddress, Locator::SELECTOR_XPATH)->getText();
    }

    /**
     * Get order grand total.
     *
     * @return string
     */
    public function getGrandTotal()
    {
        return $this->escapeCurrency($this->_rootElement->find($this->grandTotal)->getText());
    }

    /**
     * Method that escapes currency symbols.
     *
     * @param string $price
     * @return string|null
     */
    protected function escapeCurrency($price)
    {
        preg_match("/^\\D*\\s*([\\d,\\.]+)\\s*\\D*$/", $price, $matches);
        return (isset($matches[1])) ? $matches[1] : null;
    }

    /**
     * Get order payment method.
     *
     * @return string
     */
    public function getPaymentMethod()
    {
        return $this->_rootElement->find($this->paymentMethod, Locator::SELECTOR_XPATH)->getText();
    }

    /**
     * Get order shipping method.
     *
     * @param array $shipping
     * @return bool
     */
    public function isShippingMethodVisible(array $shipping)
    {
        return strpos(
            $this->_rootElement->find($this->shippingMethod, Locator::SELECTOR_XPATH)->getText(),
            sprintf("%s - %s",$shipping['shipping_service'], $shipping['shipping_method'])) != false;
    }

    /**
     * Is item product visible.
     *
     * @param InjectableFixture $product
     * @return bool
     */
    public function isItemVisible(InjectableFixture $product)
    {
        $productName = strtolower($product->getName());
        return $this->_rootElement->find(sprintf($this->itemProduct, $productName))->isVisible();
    }
}
