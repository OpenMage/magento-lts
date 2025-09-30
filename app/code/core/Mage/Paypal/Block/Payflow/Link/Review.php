<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/**
 * Paypal PayflowLink Express Onepage checkout block
 *
 * @package    Mage_Paypal
 * @deprecated since 1.6.2.0
 */
class Mage_Paypal_Block_Payflow_Link_Review extends Mage_Paypal_Block_Express_Review
{
    /**
     * Retrieve payment method and assign additional template values
     *
     * @return Mage_Paypal_Block_Express_Review
     */
    protected function _beforeToHtml()
    {
        return parent::_beforeToHtml();
    }
}
