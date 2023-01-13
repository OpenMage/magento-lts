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

namespace Mage\Adminhtml\Test\Block\Customer\Edit;

use Mage\Adminhtml\Test\Block\Widget\FormTabs;
use Mage\Customer\Test\Fixture\Customer;
use Mage\Customer\Test\Fixture\Address;

/**
 * Form for creation of the customer.
 */
class CustomerForm extends FormTabs
{
    /**
     * Customer groups selector.
     *
     * @var string
     */
    protected $customerGroupsSelector = "[name='account[group_id]']";

    /**
     * Fill Customer forms on tabs by customer, addresses data.
     *
     * @param Customer $customer
     * @param Address|Address[]|null $address
     * @return $this
     */
    public function fillCustomer(Customer $customer, $address = null)
    {
        if ($customer->hasData()) {
            parent::fill($customer);
        }
        if (null !== $address) {
            $this->openTab('addresses');
            $this->getTabElement('addresses')->fillAddresses($address);
        }

        return $this;
    }

    /**
     * Get data of Customer information, addresses on tabs.
     *
     * @param Customer $customer
     * @param Address|Address[]|null $address
     * @return array
     */
    public function getDataCustomer(Customer $customer, $address = null)
    {
        $data = ['customer' => $customer->hasData() ? parent::getData($customer) : parent::getData()];

        if (null !== $address) {
            $this->openTab('addresses');
            $data['addresses'] = $this->getTabElement('addresses')->getDataAddresses($address);
        }

        return $data;
    }

    /**
     * Get customer groups.
     *
     * @return array
     */
    public function getCustomerGroups()
    {
        return explode("\n", $this->_rootElement->find($this->customerGroupsSelector)->getText());
    }
}
