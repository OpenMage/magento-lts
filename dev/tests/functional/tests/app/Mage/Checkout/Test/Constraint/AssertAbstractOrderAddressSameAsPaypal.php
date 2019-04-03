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
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Checkout\Test\Constraint;

use Mage\Adminhtml\Test\Block\Sales\Order\View\Tab\Info;
use Mage\Paypal\Test\Fixture\PaypalAddress;
use Mage\Paypal\Test\Fixture\PaypalCustomer;
use Mage\Sales\Test\Page\Adminhtml\SalesOrderIndex;
use Mage\Sales\Test\Page\Adminhtml\SalesOrderView;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Assert that address on order page in backend is same as address in PayPal.
 */
abstract class AssertAbstractOrderAddressSameAsPaypal extends AbstractConstraint
{
    /**
     * Address type.
     *
     * @var string
     */
    protected $addressType;

    /**
     * Customer address pattern.
     *
     * @var array
     */
    protected $addressPattern = [
        ['suffix', 'firstname', 'middlename', 'lastname', 'prefix'],
        ['street'],
        'implode_with_comma' => ['city', 'region', 'region_id', 'postcode'],
        ['country_id'],
        ['telephone']
    ];

    /**
     * Assert that address on order page in backend is same as address in PayPal.
     *
     * @param SalesOrderIndex $salesOrderIndex
     * @param SalesOrderView $salesOrderView
     * @param PaypalCustomer $paypalCustomer
     * @param string $orderId
     * @return void
     */
    public function processAssert(
        SalesOrderIndex $salesOrderIndex,
        SalesOrderView $salesOrderView,
        PaypalCustomer $paypalCustomer,
        $orderId
    )
    {
        $addressBlock = "get{$this->addressType}AddressBlock";
        $salesOrderIndex->open()->getSalesOrderGrid()->searchAndOpen(['id' => $orderId]);
        /** @var Info $informationTab */
        $informationTab = $salesOrderView->getOrderForm()->getTabElement('information');
        $orderAddress = $informationTab->$addressBlock()->getAddress();
        $payPalShippingAddress = $this->prepareCustomerAddress($paypalCustomer, $orderAddress);

        \PHPUnit_Framework_Assert::assertEquals($payPalShippingAddress, $orderAddress);
    }

    /**
     * Prepare customer address for assert.
     *
     * @param PaypalCustomer $customer
     * @param $orderAddress
     * @return string
     */
    protected function prepareCustomerAddress(PaypalCustomer $customer, $orderAddress)
    {
        /** @var PaypalAddress $customerAddress */
        $customerAddress = $customer->getDataFieldConfig('address')['source']->getAddresses()[0];

        $availableFields = [];
        foreach ($this->addressPattern as $key => $lineItems) {
            foreach ($lineItems as $lineItem) {
                if ($customerAddress->hasData($lineItem)) {
                    $availableFields[$key][] = $customerAddress->getData($lineItem);
                }
            }
            $availableFields[$key] = ($key === 'implode_with_comma')
                ? implode(', ', $availableFields[$key])
                : implode(' ', $availableFields[$key]);

        }
        $availableFields = $this->prepareCustomerPhone($availableFields, $orderAddress);
        $availableFields = implode("\n", $availableFields);

        return $availableFields;
    }

    /**
     * Remove customer telephone number, if telephone number isn't sent from PayPal side
     *
     * @param $availableFields
     * @param $orderAddress
     * @return mixed
     * @internal param $customerPhone
     * @internal param $orderBillingAddress
     * @internal param $payPalShippingAddress
     */
    protected function prepareCustomerPhone($availableFields, $orderAddress)
    {
        if (strpos($orderAddress, $availableFields[3]) == false) {
            unset($availableFields[3]);
        }
        return $availableFields;
    }
}
