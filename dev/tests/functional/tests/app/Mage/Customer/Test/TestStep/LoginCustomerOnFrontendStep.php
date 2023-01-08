<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Customer\Test\TestStep;

use Mage\Cms\Test\Page\CmsIndex;
use Mage\Customer\Test\Fixture\Customer;
use Mage\Customer\Test\Page\CustomerAccountLogin;
use Magento\Mtf\TestStep\TestStepInterface;

/**
 * Login customer on frontend.
 */
class LoginCustomerOnFrontendStep implements TestStepInterface
{
    /**
     * Customer fixture.
     *
     * @var Customer
     */
    protected $customer;

    /**
     * Cms index page.
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * Customer login page.
     *
     * @var CustomerAccountLogin
     */
    protected $customerAccountLogin;

    /**
     * @constructor
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountLogin $customerAccountLogin
     * @param Customer $customer
     */
    public function __construct(
        CmsIndex $cmsIndex,
        CustomerAccountLogin $customerAccountLogin,
        Customer $customer
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->customerAccountLogin = $customerAccountLogin;
        $this->customer = $customer;
    }

    /**
     * Login customer.
     *
     * @return void
     */
    public function run()
    {
        $this->cmsIndex->open();
        $this->cmsIndex->getTopLinksBlock()->openAccount();
        if ($this->cmsIndex->getLinksBlock()->isLinkVisible("Log Out")) {
            $this->cmsIndex->getLinksBlock()->openLink("Log Out");
            $this->cmsIndex->getCmsPageBlock()->waitUntilTextIsVisible('Home Page');
            $this->cmsIndex->getTopLinksBlock()->openAccount();
        }
        $this->cmsIndex->getLinksBlock()->openLink("Log In");
        $this->customerAccountLogin->getLoginBlock()->login($this->customer);
    }
}
