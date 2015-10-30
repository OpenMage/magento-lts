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

use Mage\Paypal\Test\Fixture\PaypalCustomer;
use Mage\Paypal\Test\Page\Paypal;
use Magento\Mtf\TestStep\TestStepInterface;
use Magento\Mtf\Client\Browser;

/**
 * Login to Pay Pal step.
 */
class LoginToPayPalStep implements TestStepInterface
{
    /**
     * Pay Pal page.
     *
     * @var Paypal
     */
    protected $paypalPage;

    /**
     * Pay Pal customer fixture.
     *
     * @var PaypalCustomer
     */
    protected $customer;

    /**
     * Browser.
     *
     * @var Browser
     */
    protected $browser;

    /**
     * Loader selector.
     *
     * @var string
     */
    protected $loader = '#spinner .loader';

    /**
     * @constructor
     * @param Browser $browser
     * @param Paypal $paypalPage
     * @param PaypalCustomer $paypalCustomer
     */
    public function __construct(Browser $browser, Paypal $paypalPage, PaypalCustomer $paypalCustomer)
    {
        $this->browser = $browser;
        $this->paypalPage = $paypalPage;
        $this->customer = $paypalCustomer;
    }

    /**
     * Login to Pay Pal.
     *
     * @return void
     */
    public function run()
    {
        $browser = $this->browser;
        $selector = $this->loader;
        $browser->waitUntil(
            function () use ($browser, $selector) {
                $element = $browser->find($selector);
                return $element->isVisible() == false ? true : null;
            }
        );
        /** Log out from previous session. */
        $reviewBlock = $this->paypalPage->getReviewBlock()->isVisible()
            ? $this->paypalPage->getReviewBlock()
            : $this->paypalPage->getOldReviewBlock();
        $reviewBlock->logOut();

        $payPalLoginBlock = $this->paypalPage->getLoginBlock()->isVisible()
            ? $this->paypalPage->getLoginBlock()
            : $this->paypalPage->getOldLoginBlock();
        $payPalLoginBlock->fill($this->customer);
        $payPalLoginBlock->submit();
        $payPalLoginBlock->switchOffPayPalFrame();
        $reviewBlock->waitLoader();
    }
}
