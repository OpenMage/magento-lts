<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Checkout
 */

/**
 * Cart Item Configure block
 * Updates templates and blocks to show 'Update Cart' button and set right form submit url
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @module     Checkout
 */
class Mage_Checkout_Block_Cart_Item_Configure extends Mage_Core_Block_Template
{
    /**
     * Configure product view blocks
     *
     * @inheritDoc
     * @throws Exception
     */
    protected function _prepareLayout()
    {
        // Set custom submit url route for form - to submit updated options to cart
        $block = $this->getLayout()->getBlock('product.info');
        if ($block) {
            $block->setSubmitRouteData([
                'route' => 'checkout/cart/updateItemOptions',
                'params' => ['id' => $this->getRequest()->getParam('id')],
            ]);
        }

        // Set custom template with 'Update Cart' button
        $block = $this->getLayout()->getBlock('product.info.addtocart');
        if ($block) {
            $block->setTemplate('checkout/cart/item/configure/updatecart.phtml');
        }

        return parent::_prepareLayout();
    }
}
