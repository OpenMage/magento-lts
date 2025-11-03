<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Checkout
 */

/**
 * Minicart block
 *
 * @package    Mage_Checkout
 */
class Mage_Checkout_Block_Cart_Minicart extends Mage_Checkout_Block_Cart_Abstract
{
    /**
     * Get shopping cart items qty based on configuration (summary qty or items qty)
     *
     * @return float|int
     */
    public function getSummaryCount()
    {
        if ($this->getData('summary_qty')) {
            return $this->getData('summary_qty');
        }

        return Mage::getSingleton('checkout/cart')->getSummaryQty();
    }
}
