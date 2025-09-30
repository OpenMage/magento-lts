<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_PaypalUk
 */

/**
 * @package    Mage_PaypalUk
 */
class Mage_PaypalUk_Block_Express_Form extends Mage_Paypal_Block_Express_Form
{
    /**
     * Payment method code
     * @var string
     */
    protected $_methodCode = Mage_Paypal_Model_Config::METHOD_WPP_PE_EXPRESS;
}
