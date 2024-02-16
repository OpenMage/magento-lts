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

namespace Mage\GiftMessage\Test\Constraint;

use Mage\Customer\Test\Fixture\Customer;
use Mage\Customer\Test\Page\CustomerAccountLogout;
use Mage\GiftMessage\Test\Fixture\GiftMessage;
use Mage\Sales\Test\Page\OrderHistory;
use Mage\Sales\Test\Page\OrderView;

/**
 * Assert that message from dataset is displayed for each items on order view page on frontend.
 */
class AssertGiftMessageInFrontendOrderItems extends AbstractAssertGiftMessageOnFrontend
{
    /* tags */
    const SEVERITY = 'high';
    /* end tags */

    /**
     * Assert that message from dataset is displayed for each items on order view page on frontend.
     *
     * @param GiftMessage $giftMessage
     * @param Customer $customer
     * @param OrderHistory $orderHistory
     * @param OrderView $orderView
     * @param CustomerAccountLogout $customerAccountLogout
     * @param string $orderId
     * @param array $products
     * @return void
     */
    public function processAssert(
        GiftMessage $giftMessage,
        Customer $customer,
        OrderHistory $orderHistory,
        OrderView $orderView,
        CustomerAccountLogout $customerAccountLogout,
        $orderId,
        array $products = []
    ) {
        $this->loginOnFrontend($customer);
        $this->openOrderPage($orderHistory, $orderId);
        $expectedData = $this->prepareExpectedData($giftMessage);
        $actualData = $this->prepareActualData($orderView, $products);

        \PHPUnit_Framework_Assert::assertEquals($expectedData, $actualData);

        $customerAccountLogout->open();
    }

    /**
     * Prepare actual data.
     *
     * @param OrderView $orderView
     * @param array $products
     * @return array
     */
    protected function prepareActualData(OrderView $orderView, array $products)
    {
        $result = [];
        foreach ($products as $key => $product) {
            $result[$key] = $orderView->getGiftMessageForItemBlock()->getItemGiftMessage($product);
        }

        return $result;
    }

    /**
     * Prepare expected data.
     *
     * @param GiftMessage $giftMessage
     * @return array
     */
    protected function prepareExpectedData(GiftMessage $giftMessage)
    {
        $result = [];
        if ($giftMessage->hasData('items')) {
            $giftMessageItems = $giftMessage->getItems();
            foreach ($giftMessageItems as $key => $itemGiftMessage) {
                $result[$key] = parent::prepareExpectedData($itemGiftMessage);
            }
        } else {
            $result = parent::prepareExpectedData($giftMessage);
        }

        return $result;
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return "Gift message is displayed for each items on order view page on frontend correctly.";
    }
}
