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

namespace Mage\Customer\Test\TestCase;

use Mage\Cms\Test\Page\CmsIndex;
use Mage\Customer\Test\Fixture\Customer;
use Mage\Customer\Test\Page\CustomerAccountEdit;
use Mage\Customer\Test\Page\CustomerAccountIndex;
use Mage\Customer\Test\Page\CustomerAccountLogin;
use Magento\Mtf\TestCase\Injectable;

/**
 * Preconditions:
 * 1. Create customer.
 *
 * Steps:
 * 1. Login to fronted as customer from preconditions.
 * 2. Navigate to My Account page.
 * 3. Click "Change Password" link near "Contact Information".
 * 4. Fill form according to data set and save.
 * 5. Perform all assertions.
 *
 * @group Customer_Account_(CS)
 * @ZephyrId MPERF-7485
 */
class ChangeCustomerPasswordTest extends Injectable
{
    /**
     * CmsIndex page.
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * CustomerAccountLogin page.
     *
     * @var CustomerAccountLogin
     */
    protected $customerAccountLogin;

    /**
     * CustomerAccountIndex page.
     *
     * @var CustomerAccountIndex
     */
    protected $customerAccountIndex;

    /**
     * CustomerAccountEdit page.
     *
     * @var CustomerAccountEdit
     */
    protected $customerAccountEdit;

    /**
     * Preparing pages for test.
     *
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountLogin $customerAccountLogin
     * @param CustomerAccountIndex $customerAccountIndex
     * @param CustomerAccountEdit $customerAccountEdit
     * @return void
     */
    public function __inject(
        CmsIndex $cmsIndex,
        CustomerAccountLogin $customerAccountLogin,
        CustomerAccountIndex $customerAccountIndex,
        CustomerAccountEdit $customerAccountEdit
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->customerAccountLogin = $customerAccountLogin;
        $this->customerAccountIndex = $customerAccountIndex;
        $this->customerAccountEdit = $customerAccountEdit;
    }

    /**
     * Run Change customer password test.
     *
     * @param Customer $initialCustomer
     * @param Customer $customer
     * @return void
     */
    public function test(Customer $initialCustomer, Customer $customer)
    {
        // Preconditions
        $initialCustomer->persist();

        // Steps
        $this->objectManager->create(
            'Mage\Customer\Test\TestStep\LoginCustomerOnFrontendStep',
            ['customer' => $initialCustomer]
        )->run();

        $this->cmsIndex->getTopLinksBlock()->openAccount();
        $this->cmsIndex->getLinksBlock()->openLink('My Account');
        $this->customerAccountIndex->getInfoBlock()->openChangePassword();
        $this->customerAccountEdit->getAccountInfoForm()->fill($customer);
        $this->customerAccountEdit->getAccountInfoForm()->submit();
    }
}
