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

namespace Mage\Authorizenet\Test\TestStep;

use Magento\Mtf\Fixture\FixtureFactory;
use Mage\Checkout\Test\Page\CheckoutOnepage;
use Magento\Mtf\TestStep\TestStepInterface;
use Mage\Payment\Test\Fixture\Cc;

/**
 * Fill credit card.
 */
class FillCreditCardStep implements TestStepInterface
{
    /**
     * Onepage checkout page.
     *
     * @var CheckoutOnepage
     */
    protected $checkoutOnepage;

    /**
     * Payment information.
     *
     * @var string
     */
    protected $payment;

    /**
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param CheckoutOnepage $checkoutOnepage
     * @param array $payment
     */
    public function __construct(
        FixtureFactory $fixtureFactory,
        CheckoutOnepage $checkoutOnepage,
        array $payment
    ) {
        if (isset($payment['cc']) && !($payment['cc'] instanceof Cc)) {
            $payment['cc'] = $fixtureFactory->create('Mage\Payment\Test\Fixture\Cc', ['dataset' => $payment['cc']]);
        }
        $this->payment = $payment;
        $this->checkoutOnepage = $checkoutOnepage;
    }

    /**
     * Fill credit card step.
     *
     * @return void
     */
    public function run()
    {
        $this->checkoutOnepage->getAuthorizenetDirectpostForm()->fill($this->payment['cc']);
    }
}
