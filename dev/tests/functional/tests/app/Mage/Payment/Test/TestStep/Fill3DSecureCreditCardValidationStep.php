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

namespace Mage\Payment\Test\TestStep;

use Mage\Checkout\Test\Page\CheckoutOnepage;
use Mage\Payment\Test\Fixture\Cc;
use Magento\Mtf\TestStep\TestStepInterface;
use Magento\Mtf\ObjectManager;

/**
 * Fill 3D secure credit card validation.
 */
class Fill3DSecureCreditCardValidationStep implements TestStepInterface
{
    /**
     * Onepage checkout page.
     *
     * @var CheckoutOnepage
     */
    protected $checkoutOnepage;

    /**
     * Cc fixture.
     *
     * @var Cc
     */
    protected $cc;

    /**
     * @constructor
     * @param CheckoutOnepage $checkoutOnepage
     * @param array $payment
     */
    public function __construct(CheckoutOnepage $checkoutOnepage, array $payment)
    {
        $this->checkoutOnepage = $checkoutOnepage;
        $this->cc = $payment['cc'];
    }

    /**
     * Fill 3D secure credit card validation.
     *
     * @return void
     */
    public function run()
    {
        $centinelForm = $this->checkoutOnepage->getReviewBlock()->getCentinelForm();
        $centinelForm->fillCc($this->cc);
        $centinelForm->submitCode();
    }
}
