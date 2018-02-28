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
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Checkout\Test\TestStep;

use Mage\Checkout\Test\Page\CheckoutMultishippingLogin;
use Mage\Checkout\Test\Page\CheckoutMultishippingRegister;
use Mage\Customer\Test\Fixture\Customer;
use Magento\Mtf\TestStep\TestStepInterface;

/**
 * Selecting checkout with multishipping method.
 */
class SelectCheckoutMethodWithMultishippingStep implements TestStepInterface
{
    /**
     * Checkout multishipping login page.
     *
     * @var CheckoutMultishippingLogin
     */
    protected $checkoutMultishippingLogin;

    /**
     * Checkout multishipping register page.
     *
     * @var CheckoutMultishippingRegister
     */
    protected $checkoutMultishippingRegister;

    /**
     * Customer fixture.
     *
     * @var Customer
     */
    protected $customer;

    /**
     * Checkout method.
     *
     * @var string
     */
    protected $checkoutMethod;

    /**
     * @constructor
     * @param CheckoutMultishippingLogin $checkoutMultishippingLogin
     * @param CheckoutMultishippingRegister $checkoutMultishippingRegister
     * @param Customer $customer
     * @param string $checkoutMethod
     */
    public function __construct(
        CheckoutMultishippingLogin $checkoutMultishippingLogin,
        CheckoutMultishippingRegister $checkoutMultishippingRegister,
        Customer $customer,
        $checkoutMethod
    ) {
        $this->checkoutMultishippingLogin = $checkoutMultishippingLogin;
        $this->checkoutMultishippingRegister = $checkoutMultishippingRegister;
        $this->checkoutMethod = $checkoutMethod;
        $this->customer = $customer;
    }

    /**
     * Run step that selecting checkout method.
     *
     * @return void
     * @throws \Exception
     */
    public function run()
    {
        $checkoutMethodBlock = $this->checkoutMultishippingLogin->getLoginBlock();
        switch ($this->checkoutMethod) {
            case 'register':
                $checkoutMethodBlock->createNewAccount();
                $this->checkoutMultishippingRegister->getRegisterForm()->registerCustomer($this->customer);
                break;
            case 'login':
                $checkoutMethodBlock->login($this->customer);
                break;
            default:
                throw new \Exception("Undefined checkout method.");
                break;
        }
    }
}
