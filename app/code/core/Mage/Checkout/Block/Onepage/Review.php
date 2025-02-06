<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Checkout
 */

/**
 * One page checkout status
 *
 * @category   Mage
 * @package    Mage_Checkout
 */
class Mage_Checkout_Block_Onepage_Review extends Mage_Checkout_Block_Onepage_Abstract
{
    protected function _construct()
    {
        $this->getCheckout()->setStepData('review', [
            'label'     => Mage::helper('checkout')->__('Order Review'),
            'is_show'   => $this->isShow(),
        ]);
        parent::_construct();

        $this->getQuote()->collectTotals()->save();
    }
}
