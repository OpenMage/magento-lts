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

namespace Mage\Checkout\Test\TestStep;

use Mage\Checkout\Test\Page\CheckoutMultishippingShipping;
use Magento\Mtf\TestStep\TestStepInterface;

/**
 * Fill shipping information with multishipping.
 */
class FillShippingMethodWithMultishippingStep implements TestStepInterface
{
    /**
     * Checkout multishipping shipping page.
     *
     * @var CheckoutMultishippingShipping
     */
    protected $checkoutMultishippingShipping;

    /**
     * Shipping carrier and method.
     *
     * @var array
     */
    protected $shippingData;

    /**
     * Addresses' fixtures.
     *
     * @var array
     */
    protected $addresses;

    /**
     * @constructor
     * @param CheckoutMultishippingShipping $checkoutMultishippingShipping
     * @param array|null $shippingData
     * @param array $addresses [optional]
     */
    public function __construct(
        CheckoutMultishippingShipping $checkoutMultishippingShipping,
        $shippingData = null,
        array $addresses = []
    ) {
        $this->checkoutMultishippingShipping = $checkoutMultishippingShipping;
        $this->shippingData = $shippingData;
        $this->addresses = $addresses;
    }

    /**
     * Select shipping method.
     *
     * @return void
     */
    public function run()
    {
        if ($this->shippingData !== null) {
            foreach ($this->addresses as $key => $address) {
                $this->checkoutMultishippingShipping->getShippingBlock()->getItemsBlock()->getItemBlock($address, $key)
                    ->fillShippingMethod($this->shippingData[$key]);
            }
        }
        $this->checkoutMultishippingShipping->getShippingBlock()->clickContinueButton();
    }
}
