<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

/**
 * PayPal Debug Model
 */
class Mage_Paypal_Model_Debug extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('paypal/debug');
    }
}
