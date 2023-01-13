<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Checkout\Test\TestStep;

use Mage\Checkout\Test\Page\CheckoutOnepage;
use Magento\Mtf\TestStep\TestStepInterface;

/**
 * Fill shipping information.
 */
class FillShippingMethodStep implements TestStepInterface
{
    /**
     * Onepage checkout page.
     *
     * @var CheckoutOnepage
     */
    protected $checkoutOnepage;

    /**
     * Shipping carrier and method.
     *
     * @var array
     */
    protected $shipping;

    /**
     * @constructor
     * @param CheckoutOnepage $checkoutOnepage
     * @param array $shipping
     */
    public function __construct(CheckoutOnepage $checkoutOnepage, array $shipping)
    {
        $this->checkoutOnepage = $checkoutOnepage;
        $this->shipping = $shipping;
    }

    /**
     * Select shipping method.
     *
     * @return void
     */
    public function run()
    {
        if ($this->shipping['shipping_service'] !== '-') {
            $this->checkoutOnepage->getShippingMethodBlock()->selectShippingMethod($this->shipping);
            $this->checkoutOnepage->getShippingMethodBlock()->clickContinue();
        }
    }
}
