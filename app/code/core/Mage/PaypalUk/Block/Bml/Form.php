<?php
/**
 * Bill Me Later payment form
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_PaypalUk
 */
class Mage_PaypalUk_Block_Bml_Form extends Mage_Paypal_Block_Bml_Form
{
    /**
     * Payment method code
     * @var string
     */
    protected $_methodCode = Mage_Paypal_Model_Config::METHOD_WPP_PE_BML;
}
