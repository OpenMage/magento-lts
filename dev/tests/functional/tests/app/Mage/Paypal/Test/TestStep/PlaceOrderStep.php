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

namespace Mage\Paypal\Test\TestStep;

use Mage\Paypal\Test\Page\PaypalExpressReview;
use Mage\Checkout\Test\Page\CheckoutOnepageSuccess;
use Magento\Mtf\TestStep\TestStepInterface;

/**
 * Place order in Pay Pal checkout.
 */
class PlaceOrderStep implements TestStepInterface
{
    /**
     * Pay Pal express review page.
     *
     * @var PaypalExpressReview
     */
    protected $paypalExpressReview;

    /**
     * One page checkout success page.
     *
     * @var CheckoutOnepageSuccess
     */
    protected $checkoutOnepageSuccess;

    /**
     * Shipping method.
     *
     * @var string
     */
    protected $shippingMethod;

    /**
     * @constructor
     * @param PaypalExpressReview $paypalExpressReview
     * @param CheckoutOnepageSuccess $checkoutOnepageSuccess
     * @param string $shippingMethod
     */
    public function __construct(
        PaypalExpressReview $paypalExpressReview,
        CheckoutOnepageSuccess $checkoutOnepageSuccess,
        $shippingMethod
    ) {
        $this->paypalExpressReview = $paypalExpressReview;
        $this->checkoutOnepageSuccess = $checkoutOnepageSuccess;
        $this->shippingMethod = $shippingMethod;
    }

    /**
     * Place order after checking order totals on review step.
     *
     * @return array
     */
    public function run()
    {
        $orderReviewBlock = $this->paypalExpressReview->getReviewBlock();
        if ('-' !== $this->shippingMethod) {
            $orderReviewBlock->selectShippingMethod($this->shippingMethod);
        }
        $orderReviewBlock->placeOrder();
        return ['orderId' => $this->checkoutOnepageSuccess->getSuccessBlock()->getGuestOrderId()];
    }
}
