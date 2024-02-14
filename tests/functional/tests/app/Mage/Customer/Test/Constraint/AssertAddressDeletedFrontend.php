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

namespace Mage\Customer\Test\Constraint;

use Mage\Customer\Test\Page\CustomerAccountIndex;
use Mage\Customer\Test\Page\CustomerAddress;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Assert that deleted customers address is absent in Address Book in Customer Account.
 */
class AssertAddressDeletedFrontend extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Expected message.
     */
    const EXPECTED_MESSAGE = 'You have no additional address entries in your address book.';

    /**
     * Asserts that 'Additional Address Entries' contains expected message.
     *
     * @param CustomerAccountIndex $customerAccountIndex
     * @param CustomerAddress $customerAddress
     * @return void
     */
    public function processAssert(CustomerAccountIndex $customerAccountIndex, CustomerAddress $customerAddress)
    {
        $customerAccountIndex->open();
        $customerAccountIndex->getAccountNavigationBlock()->openNavigationItem('Address Book');
        \PHPUnit_Framework_Assert::assertEquals(
            self::EXPECTED_MESSAGE,
            $customerAddress->getBookBlock()->getAdditionalAddressBlock()->getBlockText(),
            'Expected text is absent in Additional Address block.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Deleted address is absent in "Additional Address Entries" block.';
    }
}
