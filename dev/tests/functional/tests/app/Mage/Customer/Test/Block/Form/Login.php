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

namespace Mage\Customer\Test\Block\Form;

use Magento\Mtf\Block\Form;
use Magento\Mtf\Client\Locator;
use Mage\Customer\Test\Fixture\Customer;

/**
 * Form for frontend login.
 */
class Login extends Form
{
    /**
     * Login button for registered customers.
     *
     * @var string
     */
    protected $loginButton = '#send2';

    /**
     * Selector for 'Create an Account' button.
     *
     * @var string
     */
    protected $newAccount = 'a[href*="register"]';

    /**
     * Login customer in the Frontend.
     *
     * @param Customer $customer
     *
     * @SuppressWarnings(PHPMD.ConstructorWithNameAsEnclosingClass)
     */
    public function login(Customer $customer)
    {
        $this->fill($customer);
        $this->submit();
        $this->waitForElementNotVisible($this->loginButton, Locator::SELECTOR_CSS);
    }

    /**
     * Submit login form.
     */
    public function submit()
    {
        $this->_rootElement->find($this->loginButton, Locator::SELECTOR_CSS)->click();
    }

    /**
     * Click on 'Create an Account' button.
     *
     * @return void
     */
    public function createNewAccount()
    {
        $this->_rootElement->find($this->newAccount)->click();
    }
}
