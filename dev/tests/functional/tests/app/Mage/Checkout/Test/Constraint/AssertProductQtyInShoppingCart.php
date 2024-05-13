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

namespace Mage\Checkout\Test\Constraint;

use Mage\Checkout\Test\Block\Cart\CartItem;

/**
 * Assert that quantity in the shopping cart is equals to expected quantity from data set.
 */
class AssertProductQtyInShoppingCart extends AbstractAssertProductInShoppingCart
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Data type.
     *
     * @var string
     */
    protected $dataType = 'qty';

    /**
     * Get cart data.
     *
     * @param CartItem $cartItem
     * @return array
     */
    protected function getCartData(CartItem $cartItem)
    {
        return [$this->dataType => $cartItem->getQty()];
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Quantity in the shopping cart equals to expected quantity from data set.';
    }
}
