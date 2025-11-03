<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml sales order create sidebar viewed block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Sidebar_Viewed extends Mage_Adminhtml_Block_Sales_Order_Create_Sidebar_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_sidebar_viewed');
        $this->setDataId('viewed');
    }

    /**
     * Retrieve display block availability
     *
     * @return false|int
     */
    public function canDisplay()
    {
        return false;
    }

    /**
     * Retrieve availability removing items in block
     *
     * @return false
     */
    public function canRemoveItems()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('sales')->__('Recently Viewed');
    }
}
