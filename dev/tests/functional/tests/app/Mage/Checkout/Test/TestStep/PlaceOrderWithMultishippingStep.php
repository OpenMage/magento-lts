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

use Mage\Checkout\Test\Page\CheckoutMultishippingOverview;
use Mage\Checkout\Test\Page\CheckoutMultishippingSuccess;
use Magento\Mtf\TestStep\TestStepInterface;

/**
 * Place order in checkout multishipping overview page.
 */
class PlaceOrderWithMultishippingStep implements TestStepInterface
{
    /**
     * Checkout multishipping overview page.
     *
     * @var CheckoutMultishippingOverview
     */
    protected $checkoutMultishippingOverview;

    /**
     * Checkout multishipping success page.
     *
     * @var CheckoutMultishippingSuccess
     */
    protected $checkoutMultishippingSuccess;

    /**
     * @constructor
     * @param CheckoutMultishippingOverview $checkoutMultishippingOverview
     * @param CheckoutMultishippingSuccess $checkoutMultishippingSuccess
     */
    public function __construct(
        CheckoutMultishippingOverview $checkoutMultishippingOverview,
        CheckoutMultishippingSuccess $checkoutMultishippingSuccess
    ) {
        $this->checkoutMultishippingOverview = $checkoutMultishippingOverview;
        $this->checkoutMultishippingSuccess = $checkoutMultishippingSuccess;
    }

    /**
     * Place order with multishipping.
     *
     * @return mixed
     */
    public function run()
    {
        $this->checkoutMultishippingOverview->getOverviewBlock()->clickPlaceOrder();
        $ordersIds = $this->checkoutMultishippingSuccess->getSuccessBlock()->getOrdersIds();

        return ['ordersIds' => $ordersIds];
    }
}
