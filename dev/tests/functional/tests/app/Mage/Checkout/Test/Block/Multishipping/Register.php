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
