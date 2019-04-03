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

use Mage\Checkout\Test\Page\CheckoutCart;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Assert that after adding products by sku to shopping cart products are not editable.
 */
class AssertProductIsNotEditable extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that after adding products by sku to shopping cart products are not editable.
     *
     * @param CheckoutCart $checkoutCart
     * @param array $products
     * @return void
     */
    public function processAssert(CheckoutCart $checkoutCart, array $products)
    {
        foreach ($products as $product) {
            \PHPUnit_Framework_Assert::assertFalse(
                $checkoutCart->getCartBlock()->getCartItem($product)->isEditButtonVisible(),
                'Added product is editable.'
            );
        }
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Added products by sku to shopping cart are not editable.';
    }
}
