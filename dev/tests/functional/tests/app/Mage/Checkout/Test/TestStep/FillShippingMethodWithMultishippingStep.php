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
