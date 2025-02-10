<?php
/**
 * Payflow Advanced iframe block
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Paypal
 */
class Mage_Paypal_Block_Payflow_Advanced_Form extends Mage_Paypal_Block_Payflow_Link_Form
{
    /**
     * Set payment method code
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('paypal/payflowadvanced/info.phtml');
    }

    /**
     * Get frame action URL
     *
     * @return string
     */
    public function getFrameActionUrl()
    {
        return $this->getUrl('paypal/payflowadvanced/form', ['_secure' => true]);
    }
}
