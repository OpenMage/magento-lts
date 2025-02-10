<?php
/**
 * Hosted Pro link form
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @license Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */
class Mage_Paypal_Block_Hosted_Pro_Form extends Mage_Payment_Block_Form
{
    /**
     * Set info template for payment step
     *
    */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('paypal/hss/info.phtml');
    }
}
