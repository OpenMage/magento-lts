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

namespace Mage\Customer\Test\TestCase;

use Magento\Mtf\TestCase\Injectable;
use Mage\Cms\Test\Page\CmsIndex;
use Mage\Customer\Test\Fixture\Customer;
use Mage\Customer\Test\Page\CustomerAccountCreate;
use Mage\Customer\Test\Page\CustomerAccountLogout;

/**
 * Test Flow:
 * 1. Go to frontend.
 * 2. Click Register link.
 * 3. Fill registry form.
 * 4. Click 'Create account' button.
 * 5. Perform assertions.
 *
 * @group Customer_Account_(CS)
 * @ZephyrId MPERF-6625
 */
class RegisterCustomerFrontendEntityTest extends Injectable
{
    /**
     * Customer account create page.
     *
     * @var CustomerAccountCreate
     */
    protected $customerAccountCreate;

    /**
     * Customer account logout page.
     *
     * @var CustomerAccountLogout
     */
    protected $customerAccountLogout;

    /**
     * Cms index page.
     *
     * @var CmsIndex $cmsIndex
     */
    protected $cmsIndex;

    /**
     * Injection pages.
     *
     * @param CustomerAccountCreate $customerAccountCreate
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountLogout $customerAccountLogout
     * @return void
     */
    public function __inject(
        CustomerAccountCreate $customerAccountCreate,
        CmsIndex $cmsIndex,
        CustomerAccountLogout $customerAccountLogout
    ) {
        $this->customerAccountCreate = $customerAccountCreate;
        $this->cmsIndex = $cmsIndex;
        $this->customerAccountLogout = $customerAccountLogout;
    }

    /**
     * Create Customer account on frontend.
     *
     * @param Customer $customer
     * @return void
     */
    public function test(Customer $customer)
    {
        //Steps
        $this->cmsIndex->open();
        $this->cmsIndex->getTopLinksBlock()->openAccount();
        $this->cmsIndex->getLinksBlock()->openLink('Register');
        $this->customerAccountCreate->getRegisterForm()->registerCustomer($customer);
    }

    /**
     * Logout customer from frontend account.
     *
     * @return void
     */
    public function tearDown()
    {
        $this->customerAccountLogout->open();
    }
}
