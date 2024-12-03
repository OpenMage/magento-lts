<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Links block
 *
 * @category   Mage
 * @package    Mage_Checkout
 *
 * @method int getSummaryQty()
 * @method Mage_Page_Block_Template_Links getParentBlock()
 */
class Mage_Checkout_Block_Links extends Mage_Core_Block_Template
{
    /**
     * Add shopping cart link to parent block
     *
     * @return $this
     */
    public function addCartLink()
    {
        $parentBlock = $this->getParentBlock();
        if ($parentBlock && $this->isModuleOutputEnabled('Mage_Checkout')) {
            /** @var Mage_Checkout_Helper_Cart $helper */
            $helper = $this->helper('checkout/cart');
            $count = $this->getSummaryQty() ?: $helper->getSummaryCount();
            if ($count == 1) {
                $text = $this->__('My Cart (%s item)', $count);
            } elseif ($count > 0) {
                $text = $this->__('My Cart (%s items)', $count);
            } else {
                $text = $this->__('My Cart');
            }

            $parentBlock->removeLinkByUrl($this->getUrl('checkout/cart'));
            $parentBlock->addLink($text, 'checkout/cart', $text, true, [], 50, null, 'class="top-link-cart"');
        }
        return $this;
    }

    /**
     * Add link on checkout page to parent block
     *
     * @return $this
     */
    public function addCheckoutLink()
    {
        /** @var Mage_Checkout_Helper_Data $helper */
        $helper = $this->helper('checkout');
        if (!$helper->canOnepageCheckout()) {
            return $this;
        }

        $parentBlock = $this->getParentBlock();
        if ($parentBlock && $this->isModuleOutputEnabled('Mage_Checkout')) {
            $text = $this->__('Checkout');
            $parentBlock->addLink(
                $text,
                'checkout',
                $text,
                true,
                ['_secure' => true],
                60,
                null,
                'class="top-link-checkout"'
            );
        }
        return $this;
    }
}
