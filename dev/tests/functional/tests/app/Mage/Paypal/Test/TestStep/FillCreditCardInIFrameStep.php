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
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Paypal\Test\TestStep;

use Magento\Mtf\Fixture\FixtureFactory;
use Mage\Checkout\Test\Page\CheckoutOnepage;
use Mage\Checkout\Test\Page\CheckoutOnepageSuccess;
use Magento\Mtf\TestStep\TestStepInterface;
use Magento\Mtf\Client\Browser;
use Magento\Mtf\Client\Locator;
use Mage\Payment\Test\Fixture\Cc;

/**
 * Fill credit card in Iframe step.
 */
class FillCreditCardInIFrameStep implements TestStepInterface
{
    /**
     * Onepage checkout page.
     *
     * @var CheckoutOnepage
     */
    protected $checkoutOnepage;

    /**
     * Onepage checkout success page.
     *
     * @var CheckoutOnepageSuccess
     */
    protected $checkoutOnepageSuccess;

    /**
     * Interface Browser.
     *
     * @var Browser
     */
    protected $browser;

    /**
     * I-frame selector.
     *
     * @var string
     */
    protected $iFrameSelector = '[id$="-iframe"]';

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
     * @param CheckoutOnepageSuccess $checkoutOnepageSuccess
     * @param Browser $browser
     * @param array $payment
     */
    public function __construct(
        FixtureFactory $fixtureFactory,
        CheckoutOnepage $checkoutOnepage,
        CheckoutOnepageSuccess $checkoutOnepageSuccess,
        Browser $browser,
        array $payment
    ) {
        $this->checkoutOnepage = $checkoutOnepage;
        $this->checkoutOnepageSuccess = $checkoutOnepageSuccess;
        $this->browser = $browser;
        if (isset($payment['cc']) && !($payment['cc'] instanceof Cc)) {
            $payment['cc'] = $fixtureFactory->create('Mage\Payment\Test\Fixture\Cc', ['dataset' => $payment['cc']]);
        }
        $this->payment = $payment;
    }

    /**
     * Fill credit card in i-frame step.
     *
     * @return array
     */
    public function run()
    {
        $this->checkoutOnepage->getReviewBlock()->clickContinue();
        $this->browser->switchToFrame(new Locator($this->iFrameSelector));
        $methodName = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $this->payment['method']))) . 'Form';
        $form = $this->checkoutOnepage->$methodName();
        $element = $this->browser->find('body');
        $form->fill($this->payment['cc'], $element);
        $form->clickPayNow($element);
        $this->browser->switchToFrame();

        return ['orderId' => $this->checkoutOnepageSuccess->getSuccessBlock()->getGuestOrderId()];
    }
}
