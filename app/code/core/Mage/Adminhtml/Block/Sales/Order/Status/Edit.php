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
class Mage_Adminhtml_Block_Sales_Order_Status_Edit extends Mage_Adminhtml_Block_Sales_Order_Status_New
{
    public function __construct()
    {
        parent::__construct();
        $this->_mode = 'edit';
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('sales')->__('Edit Order Status');
    }
}
