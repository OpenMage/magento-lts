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
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Customer\Test\TestCase;

use Mage\Customer\Test\Fixture\Customer;
use Mage\Customer\Test\Page\CustomerAccountIndex;
use Mage\Customer\Test\Page\CustomerAddress;
use Magento\Mtf\TestCase\Injectable;

/**
 * Preconditions:
 * 1. Customer with two addresses is created.
 *
 * Steps:
 * 1. Login as customer from preconditions.
 * 2. Go to 'Address Book' tab > Additional Address Entries.
 * 3. Click 'Delete Address' button for second address.
 * 4. Perform assertions.
 *
 * @group Customer_Account_(CS)
 * @ZephyrId MPERF-7496
 */
class DeleteCustomerAddressEntityTest extends Injectable
{
    /**
     * CustomerAccountIndex page.
     *
     * @var CustomerAccountIndex
     */
    protected $customerAccountIndex;

    /**
     * Customer address page.
     *
     * @var CustomerAddress
     */
    protected $customerAddress;

    /**
     * Injection pages.
     *
     * @param CustomerAccountIndex $customerAccountIndex
     * @param CustomerAddress $customerAddress
     * @return void
     */
    public function __inject(CustomerAccountIndex $customerAccountIndex, CustomerAddress $customerAddress)
    {
        $this->customerAccountIndex = $customerAccountIndex;
        $this->customerAddress = $customerAddress;
    }

    /**
     * Run delete customer address entity test.
     *
     * @param Customer $customer
     * @return array
     */
    public function test(Customer $customer)
    {
        // Preconditions:
        $customer->persist();
        $addressToDelete = $customer->getDataFieldConfig('address')['source']->getAddresses()[0];

        // Steps:
        $this->objectManager->create(
            'Mage\Customer\Test\TestStep\LoginCustomerOnFrontendStep',
            ['customer' => $customer]
        )->run();
        $this->customerAccountIndex->getAccountNavigationBlock()->openNavigationItem('Address Book');
        $this->customerAddress->getBookBlock()->getAdditionalAddressBlock()->deleteAddress($addressToDelete);

        return ['deletedAddress' => $addressToDelete];
    }
}
