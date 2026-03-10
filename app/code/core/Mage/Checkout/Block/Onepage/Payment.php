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
class Mage_Checkout_Block_Onepage_Payment extends Mage_Checkout_Block_Onepage_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->getCheckout()->setStepData('payment', [
            'label'     => $this->__('Payment Information'),
            'is_show'   => $this->isShow(),
        ]);
        parent::_construct();
    }

    /**
     * Getter
     *
     * @return float
     */
    public function getQuoteBaseGrandTotal()
    {
        return (float) $this->getQuote()->getBaseGrandTotal();
    }
}
