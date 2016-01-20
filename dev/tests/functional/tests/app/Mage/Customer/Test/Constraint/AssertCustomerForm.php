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

use Magento\Mtf\Constraint\AbstractConstraint;
use Mage\Customer\Test\Fixture\Customer;
use Mage\Customer\Test\Fixture\Address;
use Mage\Customer\Test\Page\Adminhtml\CustomerIndex;
use Mage\Customer\Test\Page\Adminhtml\CustomerEdit;

/**
 * Chack that displayed customer data on edit page(backend) equals passed from fixture.
 */
class AssertCustomerForm extends AbstractConstraint
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'middle';

    /**
     * Skipped fields for verify data.
     *
     * @var array
     */
    protected $customerSkippedFields = [
        'id',
        'password',
        'password_confirmation',
        'is_subscribed',
    ];

    /**
     * Assert that displayed customer data on edit page(backend) equals passed from fixture.
     *
     * @param Customer $customer
     * @param CustomerIndex $pageCustomerIndex
     * @param CustomerEdit $pageCustomerEdit
     * @param Address $address [optional]
     * @param Customer $initialCustomer [optional]
     * @return void
     */
    public function processAssert(
        Customer $customer,
        CustomerIndex $pageCustomerIndex,
        CustomerEdit $pageCustomerEdit,
        Address $address = null,
        Customer $initialCustomer = null
    ) {
        $filter = [];

        $data = $this->prepareData($customer, $initialCustomer, $address);
        $filter['email'] = $data['customer']['email'];

        $pageCustomerIndex->open();
        $pageCustomerIndex->getCustomerGridBlock()->searchAndOpen($filter);
        $dataForm = $pageCustomerEdit->getCustomerForm()->getDataCustomer($customer, $address);
        $dataDiff = $this->verify($data, $dataForm);
        \PHPUnit_Framework_Assert::assertTrue(
            empty($dataDiff),
            'Customer data on edit page(backend) not equals to passed from fixture.'
            . "\nFailed values: " . implode(', ', $dataDiff)
        );
    }

    /**
     * Prepare data.
     *
     * @param Customer $customer
     * @param Customer $initialCustomer [optional]
     * @param Address $address [optional]
     * @return array
     */
    protected function prepareData(Customer $customer, Customer $initialCustomer = null, Address $address = null)
    {
        if ($initialCustomer) {
            $data['customer'] = $customer->hasData()
                ? array_merge($initialCustomer->getData(), $customer->getData())
                : $initialCustomer->getData();
        } else {
            $data['customer'] = $customer->getData();
        }
        if ($address) {
            $data['addresses'][1] = $address->hasData() ? $address->getData() : [];
        } else {
            $data['addresses'] = [];
        }

        return $data;
    }

    /**
     * Verify data in form equals to passed from fixture.
     *
     * @param array $dataFixture
     * @param array $dataForm
     * @return array
     */
    protected function verify(array $dataFixture, array $dataForm)
    {
        $result = [];

        $customerDiff = array_diff_assoc($dataFixture['customer'], $dataForm['customer']);
        foreach ($customerDiff as $name => $value) {
            if (in_array($name, $this->customerSkippedFields)) {
                continue;
            }
            $result[] = "\ncustomer {$name}: \"{$dataForm['customer'][$name]}\" instead of \"{$value}\"";
        }
        foreach ($dataFixture['addresses'] as $key => $address) {
            $addressDiff = array_diff($address, $dataForm['addresses'][$key]);
            foreach ($addressDiff as $name => $value) {
                $result[] = "\naddress #{$key} {$name}: \"{$dataForm['addresses'][$key][$name]}"
                    . "\" instead of \"{$value}\"";
            }
        }

        return $result;
    }

    /**
     * Text success verify Customer form.
     *
     * @return string
     */
    public function toString()
    {
        return 'Displayed customer data on edit page(backend) equals to passed from fixture.';
    }
}
