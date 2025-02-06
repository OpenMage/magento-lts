<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Paypal
 */

/**
 * Paypal PayflowLink Express Onepage checkout block
 *
 * @category   Mage
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
