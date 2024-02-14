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
