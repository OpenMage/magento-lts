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

use Mage\Checkout\Test\Page\CheckoutMultishippingBilling;
use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Mtf\TestStep\TestStepInterface;
use Mage\Payment\Test\Fixture\Cc;

/**
 * Selecting payment method with multishipping.
 */
class SelectPaymentMethodWithMultishippingStep implements TestStepInterface
{
    /**
     * Onepage checkout page.
     *
     * @var CheckoutMultishippingBilling
     */
    protected $checkoutMultishippingBilling;

    /**
     * Payment information.
     *
     * @var string
     */
    protected $payment;

    /**
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param CheckoutMultishippingBilling $checkoutMultishippingBilling
     * @param array $payment
     */
    public function __construct(
        FixtureFactory $fixtureFactory,
        CheckoutMultishippingBilling $checkoutMultishippingBilling,
        array $payment
    ) {
        $this->checkoutMultishippingBilling = $checkoutMultishippingBilling;
        if (isset($payment['cc']) && !($payment['cc'] instanceof Cc)) {
            $payment['cc'] = $fixtureFactory->create('Mage\Payment\Test\Fixture\Cc', ['dataset' => $payment['cc']]);
        }
        $this->payment = $payment;
    }

    /**
     * Select payment method.
     *
     * @return array
     */
    public function run()
    {
        if ($this->payment['method'] !== 'free') {
            $this->checkoutMultishippingBilling->getBillingBlock()->selectPaymentMethod($this->payment);
        }
        $this->checkoutMultishippingBilling->getBillingBlock()->clickContinue();

        return ['payment' => $this->payment];
    }
}
