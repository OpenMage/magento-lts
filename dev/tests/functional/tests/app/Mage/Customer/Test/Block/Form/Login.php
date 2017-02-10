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
