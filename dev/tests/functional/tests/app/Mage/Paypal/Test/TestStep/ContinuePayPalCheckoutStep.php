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

use Mage\Checkout\Test\Page\CheckoutOnepageSuccess;
use Mage\Paypal\Test\Fixture\PaypalCustomer;
use Mage\Paypal\Test\Page\Paypal;
use Mage\Paypal\Test\Page\PaypalExpressReview;
use Magento\Mtf\TestStep\TestStepInterface;
use Mage\Paypal\Test\Block\AbstractReview;

/**
 * Continue Pay Pal checkout step.
 */
class ContinuePayPalCheckoutStep implements TestStepInterface
{
    /**
     * Pay Pal page.
     *
     * @var Paypal
     */
    protected $paypalPage;

    /**
     * One page checkout success page.
     *
     * @var CheckoutOnepageSuccess
     */
    protected $checkoutOnepageSuccess;

    /**
     * Pay Pal express review page.
     *
     * @var PaypalExpressReview
     */
    protected $paypalExpressReview;

    /**
     * PayPal customer.
     *
     * @var PaypalCustomer
     */
    protected $customer;

    /**
     * Review block.
     *
     * @var AbstractReview
     */
    protected $reviewBlock;

    /**
     * @constructor
     * @param Paypal $paypalPage
     * @param CheckoutOnepageSuccess $checkoutOnepageSuccess
     * @param PaypalExpressReview $paypalExpressReview
     * @param PaypalCustomer $paypalCustomer
     */
    public function __construct(
        Paypal $paypalPage,
        CheckoutOnepageSuccess $checkoutOnepageSuccess,
        PaypalExpressReview $paypalExpressReview,
        PaypalCustomer $paypalCustomer
    ) {
        $this->paypalPage = $paypalPage;
        $this->paypalExpressReview = $paypalExpressReview;
        $this->checkoutOnepageSuccess = $checkoutOnepageSuccess;
        $this->customer = $paypalCustomer;
    }

    /**
     * Continue Pay Pal checkout.
     *
     * @return array|null
     */
    public function run()
    {
        $this->reviewBlock = $this->paypalPage->getReviewBlock()->isVisible()
            ? $this->paypalPage->getReviewBlock()
            : $this->paypalPage->getOldReviewBlock();
        $this->selectCustomerAddress($this->customer);
        $this->reviewBlock->continueCheckout();
        $this->paypalExpressReview->getReviewBlock()->isPlaceOrderVisible();
        $orderId = $this->paypalExpressReview->getReviewBlock()->isPlaceOrderVisible()
            ? null
            : $this->checkoutOnepageSuccess->getSuccessBlock()->getGuestOrderId();

        return ['orderId' => $orderId];
    }

    /**
     * Select customer address.
     *
     * @param PaypalCustomer $customer
     * @return void
     */
    protected function selectCustomerAddress(PaypalCustomer $customer)
    {
        if ($this->reviewBlock->checkChangeAddressAbility()) {
            $address = $customer->getDataFieldConfig('address')['source']->getAddresses()[0];
            $this->reviewBlock->getAddressesBlock()->selectAddress($address);
            $this->reviewBlock->waitLoader();
        }
    }
}
