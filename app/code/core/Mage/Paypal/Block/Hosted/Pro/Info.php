<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Paypal
 */

/**
 * Hosted Pro link infoblock
 *
 * @category   Mage
 * @package    Mage_Paypal
 */
class Mage_Paypal_Block_Hosted_Pro_Info extends Mage_Paypal_Block_Payment_Info
{
    /**
     * Don't show CC type
     *
     * @return false
     */
    public function getCcTypeName()
    {
        return false;
    }
}
