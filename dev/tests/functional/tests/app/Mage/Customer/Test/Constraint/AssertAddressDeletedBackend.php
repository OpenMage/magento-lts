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

namespace Mage\Customer\Test\Constraint;

use Mage\Customer\Test\Fixture\Address;
use Mage\Customer\Test\Fixture\Customer;
use Mage\Sales\Test\Page\Adminhtml\SalesOrderCreateIndex;
use Mage\Sales\Test\Page\Adminhtml\SalesOrderIndex;
use Magento\Mtf\Constraint\AbstractConstraint;
use Mage\Customer\Test\Block\Address\Renderer;

/**
 * Assert that deleted customers address is not displayed on backend during order creation.
 */
class AssertAddressDeletedBackend extends AbstractConstraint
{
    /**
     * Assert that deleted customers address is not displayed on backend during order creation.
     *
     * @param SalesOrderIndex $orderIndex
     * @param SalesOrderCreateIndex $orderCreateIndex
     * @param Address $deletedAddress
     * @param Customer $customer
     * @return void
     */
    public function processAssert(
        SalesOrderIndex $orderIndex,
        SalesOrderCreateIndex $orderCreateIndex,
        Address $deletedAddress,
        Customer $customer
    ) {
        $orderIndex->open()->getPageActionsBlock()->addNew();
        $orderCreateIndex->getCustomerGrid()->selectCustomer($customer);
        $orderCreateIndex->getStoreBlock()->selectStoreView();
        \PHPUnit_Framework_Assert::assertNotContains(
            $this->prepareAddress($deletedAddress),
            $orderCreateIndex->getCreateBlock()->getBillingAddressForm()->getExistingAddresses(),
            'Deleted address is present on backend during order creation'
        );
    }

    /**
     * Prepare address for assertion.
     *
     * @param Address $address
     * @return string
     */
    protected function prepareAddress(Address $address)
    {
        /** @var Renderer $renderer */
        $renderer = $this->objectManager->create('Mage\Customer\Test\Block\Address\Renderer', ['address' => $address]);
        return $renderer->render();
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Deleted address is absent on backend during order creation';
    }
}
