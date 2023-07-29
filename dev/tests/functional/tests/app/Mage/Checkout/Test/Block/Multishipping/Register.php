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

namespace Mage\Checkout\Test\Block\Multishipping;

use Mage\Customer\Test\Fixture\Address;
use Mage\Customer\Test\Fixture\Customer;
use Magento\Mtf\Block\Form;

/**
 * Register new customer on Frontend.
 */
class Register extends Form
{
    /**
     * 'Submit' form button.
     *
     * @var string
     */
    protected $submit = '.buttons-set .button';

    /**
     * Create new customer account and fill billing address.
     *
     * @param Customer $customer
     * @return void
     */
    public function registerCustomer(Customer $customer)
    {
        $customerData = $customer->getData();
        unset($customerData['address']);
        $mapping = $this->dataMapping($customerData);
        $this->_fill($mapping);
        $address = $customer->getDataFieldConfig('address')['source']->getAddresses()[0];
        $this->fillAddress($address);
        $this->_rootElement->find($this->submit)->click();
    }

    /**
     * Fill address data.
     *
     * @param Address $address
     * @return void
     */
    protected function fillAddress(Address $address)
    {
        $skipFields = ['email', 'default_shipping'];
        $addressData = $address->getData();
        $addressData = array_flip(array_diff(array_flip($addressData), $skipFields));
        $mapping = $this->dataMapping($addressData);
        $this->_fill($mapping);
    }
}
