<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/**
 * Payflow link iframe block
 *
 * @package    Mage_Paypal
 */
class Mage_Paypal_Block_Payflow_Link_Form extends Mage_Payment_Block_Form
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('paypal/payflowlink/info.phtml');
    }

    /**
     * Get frame action URL
     *
     * @return string
     */
    public function getFrameActionUrl()
    {
        return $this->getUrl('paypal/payflow/form', ['_secure' => true]);
    }
}
