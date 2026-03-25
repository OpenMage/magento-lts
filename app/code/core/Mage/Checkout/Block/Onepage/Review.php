<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Checkout
 */

/**
 * One page checkout status
 *
 * @package    Mage_Checkout
 */
class Mage_Checkout_Block_Onepage_Review extends Mage_Checkout_Block_Onepage_Abstract
{
    /**
     * @inheritDoc
     */
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
