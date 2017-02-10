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
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
