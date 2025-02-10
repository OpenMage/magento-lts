<?php
/**
 * Payflow link infoblock
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Paypal
 */
class Mage_Paypal_Block_Payflow_Link_Info extends Mage_Paypal_Block_Payment_Info
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
