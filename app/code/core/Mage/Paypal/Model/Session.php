<?php
/**
 * Paypal transaction session namespace
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Paypal
 */
class Mage_Paypal_Model_Session extends Mage_Core_Model_Session_Abstract
{
    public function __construct()
    {
        $this->init('paypal');
    }
}
