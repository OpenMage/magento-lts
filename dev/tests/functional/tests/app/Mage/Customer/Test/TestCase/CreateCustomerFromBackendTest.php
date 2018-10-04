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

namespace Mage\Customer\Test\TestCase;

use Mage\Customer\Test\Page\Adminhtml\CustomerIndex;
use Mage\Customer\Test\Page\Adminhtml\CustomerNew;
use Mage\Customer\Test\Fixture\Address;
use Mage\Customer\Test\Fixture\Customer;
use Magento\Mtf\TestCase\Injectable;

/**
 * Test Flow:
 * 1. Log in as default admin user.
 * 2. Go to Customers > All Customers.
 * 3. Press "Add New Customer" button.
 * 4. Fill form.
 * 5. Click "Save Customer" button.
 * 6. Perform all assertions.
 *
 * @group ACL_(MX)
 * @ZephyrId MPERF-6600
 */
class CreateCustomerFromBackendTest extends Injectable
{
    /**
     * Customer index page.
     *
     * @var CustomerIndex
     */
    protected $pageCustomerIndex;

    /**
     * Customer new page.
     *
     * @var CustomerNew
     */
    protected $pageCustomerIndexNew;

    /**
     * Injection pages.
     *
     * @param CustomerIndex $pageCustomerIndex
     * @param CustomerNew $pageCustomerIndexNew
     * @return void
     */
    public function __inject(CustomerIndex $pageCustomerIndex, CustomerNew $pageCustomerIndexNew)
    {
        $this->pageCustomerIndex = $pageCustomerIndex;
        $this->pageCustomerIndexNew = $pageCustomerIndexNew;
    }

    /**
     * Creation customer from backend.
     *
     * @param Customer $customer
     * @param Address $address
     * @return void
     */
    public function test(Customer $customer, Address $address)
    {
        // Prepare data
        $address = $address->hasData() ? $address : null;

        // Steps
        $this->pageCustomerIndex->open();
        $this->pageCustomerIndex->getPageActionsBlock()->addNew();
        $this->pageCustomerIndexNew->getCustomerForm()->fillCustomer($customer, $address);
        $this->pageCustomerIndexNew->getPageActionsBlock()->save();
    }
}
