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
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Checkout\Test\Block\Cart;

use Mage\Customer\Test\Fixture\Address;
use Magento\Mtf\Block\Form;
use Magento\Mtf\Client\Locator;

/**
 * Cart shipping block.
 */
class Shipping extends Form
{
    /**
     * Estimate button selector.
     *
     * @var string
     */
    protected $estimateButton = '.buttons-set .button2';

    /**
     * Update total button selector.
     *
     * @var string
     */
    protected $updateTotalSelector = '.buttons-set .button';

    /**
     * Shipping carrier method selector.
     *
     * @var string
     */
    protected $shippingMethod = '//*[text()="%s"]/following::*//*[contains(text(), "%s")]';

    /**
     * Click Estimate button.
     *
     * @return void
     */
    protected  function clickEstimate()
    {
        $this->_rootElement->find($this->estimateButton)->click();
    }

    /**
     * Select shipping method.
     *
     * @param array $shipping
     * @return void
     */
    public function selectShippingMethod(array $shipping)
    {
        $selector = sprintf($this->shippingMethod, $shipping['shipping_service'], $shipping['shipping_method']);
        $this->_rootElement->find($selector, Locator::SELECTOR_XPATH)->click();
        $this->_rootElement->find($this->updateTotalSelector, Locator::SELECTOR_CSS)->click();
    }

    /**
     * Fill shipping and tax form.
     *
     * @param Address $address
     * @return void
     */
    public function fillEstimateShippingAndTax(Address $address)
    {
        $this->fill($address);
        $this->clickEstimate();
    }
}
