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
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Checkout\Test\Block\Onepage;

use Mage\Customer\Test\Fixture\Customer;

/**
 * One page checkout method.
 */
class Login extends AbstractOnepage
{
    /**
     * Continue checkout button.
     *
     * @var string
     */
    protected $continue = '#onepage-guest-register-button';

    /**
     * 'Checkout as Guest' radio button.
     *
     * @var string
     */
    protected $guestCheckout = '[id="login:guest"]';

    /**
     * 'Register' radio button.
     *
     * @var string
     */
    protected $registerCustomer = '[id="login:register"]';

    /**
     * Login button.
     *
     * @var string
     */
    protected $login = 'button[onclick^="onepageLogin"]';

    /**
     * Perform guest checkout.
     *
     * @return void
     */
    public function guestCheckout()
    {
        $this->_rootElement->find($this->guestCheckout)->click();
    }

    /**
     * Register customer during checkout.
     *
     * @return void
     */
    public function registerCustomer()
    {
        $this->_rootElement->find($this->registerCustomer)->click();
    }

    /**
     * Login customer during checkout.
     *
     * @param Customer $customer
     * @return void
     */
    public function loginCustomer(Customer $customer)
    {
        $this->fill($customer);
        $this->_rootElement->find($this->login)->click();
        $this->waitLoader();
    }
}
