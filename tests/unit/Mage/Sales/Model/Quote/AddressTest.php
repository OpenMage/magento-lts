<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Sales\Model\Quote;

use Mage;
use Mage_Customer_Model_Group;
use Mage_Sales_Model_Quote;
use Mage_Sales_Model_Quote_Address;
use OpenMage\Tests\Unit\OpenMageTest;

final class AddressTest extends OpenMageTest
{
    /**
     * Test that explicitly set same_as_billing flag is preserved for guest orders
     *
     * This test validates the fix for the issue where guest order shipping addresses
     * were being overwritten by billing addresses during order edit.
     *
     * @covers Mage_Sales_Model_Quote_Address::_populateBeforeSaveData
     * @group Model
     */
    public function testPreservesExplicitlySetSameAsBillingForGuestOrders(): void
    {
        // Create a quote for a guest customer (no customer ID)
        $quote = new Mage_Sales_Model_Quote();
        $quote->setCustomerId(null);
        $quote->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);

        // Create a shipping address with different data than billing
        $address = new Mage_Sales_Model_Quote_Address();
        $address->setQuote($quote);
        $address->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_SHIPPING);

        // Explicitly set same_as_billing to false (0) to indicate different addresses
        // This simulates what happens during order edit when addresses differ
        $address->setSameAsBilling(0);

        // Trigger the _populateBeforeSaveData method via _beforeSave
        // This is normally called when saving the address
        $reflectionClass = new \ReflectionClass($address);
        $method = $reflectionClass->getMethod('_populateBeforeSaveData');
        $method->invoke($address);

        // Assert that the explicitly set value was preserved
        self::assertSame(
            0,
            $address->getSameAsBilling(),
            'Explicitly set same_as_billing=0 should be preserved for guest orders during order edit',
        );
    }

    /**
     * Test that same_as_billing is set to default for new addresses without explicit value
     *
     * @covers Mage_Sales_Model_Quote_Address::_populateBeforeSaveData
     * @group Model
     */
    public function testSetsDefaultSameAsBillingForNewGuestAddressesWithoutExplicitValue(): void
    {
        // Create a quote for a guest customer (no customer ID)
        $quote = new Mage_Sales_Model_Quote();
        $quote->setCustomerId(null);
        $quote->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);

        // Create a shipping address WITHOUT explicitly setting same_as_billing
        $address = new Mage_Sales_Model_Quote_Address();
        $address->setQuote($quote);
        $address->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_SHIPPING);

        // DO NOT set same_as_billing - let the default logic handle it

        // Trigger the _populateBeforeSaveData method
        $reflectionClass = new \ReflectionClass($address);
        $method = $reflectionClass->getMethod('_populateBeforeSaveData');
        $method->invoke($address);

        // For guest orders, default behavior should set same_as_billing to 1
        self::assertSame(
            1,
            $address->getSameAsBilling(),
            'Default same_as_billing=1 should be set for new guest shipping addresses without explicit value',
        );
    }

    /**
     * Test that explicitly set same_as_billing=1 is preserved
     *
     * @covers Mage_Sales_Model_Quote_Address::_populateBeforeSaveData
     * @group Model
     */
    public function testPreservesExplicitlySetSameAsBillingTrue(): void
    {
        // Create a quote for a guest customer
        $quote = new Mage_Sales_Model_Quote();
        $quote->setCustomerId(null);
        $quote->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);

        // Create a shipping address
        $address = new Mage_Sales_Model_Quote_Address();
        $address->setQuote($quote);
        $address->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_SHIPPING);

        // Explicitly set same_as_billing to true (1)
        $address->setSameAsBilling(1);

        // Trigger the _populateBeforeSaveData method
        $reflectionClass = new \ReflectionClass($address);
        $method = $reflectionClass->getMethod('_populateBeforeSaveData');
        $method->invoke($address);

        // Assert that the explicitly set value was preserved
        self::assertSame(
            1,
            $address->getSameAsBilling(),
            'Explicitly set same_as_billing=1 should be preserved',
        );
    }

    /**
     * Test that the fix works for registered customers with different addresses
     *
     * @covers Mage_Sales_Model_Quote_Address::_populateBeforeSaveData
     * @group Model
     */
    public function testPreservesExplicitlySetSameAsBillingForRegisteredCustomers(): void
    {
        // Create a quote for a registered customer
        $quote = new Mage_Sales_Model_Quote();
        $quote->setCustomerId(123); // Registered customer

        // Create a shipping address
        $address = new Mage_Sales_Model_Quote_Address();
        $address->setQuote($quote);
        $address->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_SHIPPING);

        // Explicitly set same_as_billing to false
        $address->setSameAsBilling(0);

        // Trigger the _populateBeforeSaveData method
        $reflectionClass = new \ReflectionClass($address);
        $method = $reflectionClass->getMethod('_populateBeforeSaveData');
        $method->invoke($address);

        // Assert that the explicitly set value was preserved
        self::assertSame(
            0,
            $address->getSameAsBilling(),
            'Explicitly set same_as_billing=0 should be preserved for registered customers',
        );
    }
}
