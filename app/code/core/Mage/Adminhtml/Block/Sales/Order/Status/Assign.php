<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Status_Assign extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_controller = 'sales_order_status';
        $this->_mode       = 'assign';
        parent::__construct();
        $this->_updateButton(self::BUTTON_TYPE_SAVE, 'label', Mage::helper('sales')->__('Save Status Assignment'));
        $this->_removeButton(self::BUTTON_TYPE_DELETE);
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return string
     */
    #[Override]
    public function getHeaderText()
    {
        return Mage::helper('sales')->__('Assign Order Status to State');
    }
}
