<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

class Mage_Paypal_Block_Adminhtml_Webhook extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'paypal';
        $this->_controller = 'adminhtml_webhook';
        $this->_headerText = Mage::helper('paypal')->__('PayPal Webhook Events');
        parent::__construct();
        $this->_removeButton(self::BUTTON_TYPE_ADD);
    }
}
