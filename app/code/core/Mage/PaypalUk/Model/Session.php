<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_PaypalUk
 */

/**
 *
 * PaypalUk transaction session namespace
 *
 * @package    Mage_PaypalUk
 */
class Mage_PaypalUk_Model_Session extends Mage_Core_Model_Session_Abstract
{
    public function __construct()
    {
        $this->init('paypaluk');
    }
}
