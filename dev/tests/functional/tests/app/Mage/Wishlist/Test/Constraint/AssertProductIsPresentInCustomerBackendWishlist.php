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

namespace Mage\Wishlist\Test\Constraint;

use Mage\Customer\Test\Fixture\Customer;
use Mage\Customer\Test\Page\Adminhtml\CustomerIndex;
use Mage\Customer\Test\Page\Adminhtml\CustomerEdit;
use Magento\Mtf\Constraint\AbstractConstraint;
use Magento\Mtf\Fixture\InjectableFixture;
use Mage\Adminhtml\Test\Block\Wishlist\Customer\Edit\Tab\Wishlist\Grid;

/**
 * Assert that product added to wishlist is present on Customers account on backend
 * - in section Customer Activities - Wishlist.
 */
class AssertProductIsPresentInCustomerBackendWishlist extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that products added to wishlist are present on Customers account on backend.
     *
     * @param CustomerIndex $customerIndex
     * @param Customer $customer
     * @param CustomerEdit $customerEdit
     * @param InjectableFixture $product
     * @return void
     */
    public function processAssert(
        CustomerIndex $customerIndex,
        Customer $customer,
        CustomerEdit $customerEdit,
        InjectableFixture $product
    ) {
        $customerIndex->open();
        $customerIndex->getCustomerGridBlock()->searchAndOpen(['email' => $customer->getEmail()]);
        $customerEdit->getCustomerForm()->openTab('wishlist');
        /** @var Grid $wishlistGrid */
        $wishlistGrid = $customerEdit->getCustomerForm()->getTabElement('wishlist')->getSearchGridBlock();

        \PHPUnit_Framework_Assert::assertTrue(
            $wishlistGrid->isRowVisible(['product_name' => $product->getName()], true, false),
            $product->getName() . " is not visible in customer wishlist on backend."
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return "Product is visible in customer wishlist on backend.";
    }
}
