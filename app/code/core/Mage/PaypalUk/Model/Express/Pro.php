<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_PaypalUk
 */

/**
 * PayPal Express (Payflow Edition) implementation for payment method instances
 *
 * @package    Mage_PaypalUk
 */
class Mage_PaypalUk_Model_Express_Pro extends Mage_PaypalUk_Model_Pro
{
    /**
     * Api model type
     *
     * @var string
     */
    protected $_apiType = 'paypaluk/api_express_nvp';
}
