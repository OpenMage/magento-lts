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
            $payment['cc'] = $fixtureFactory->create('Mage\Payment\Test\Fixture\Cc', ['dataSet' => $payment['cc']]);
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
