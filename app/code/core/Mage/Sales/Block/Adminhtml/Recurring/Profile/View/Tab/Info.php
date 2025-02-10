<?php
/**
 * Recurring profile information tab
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Sales
 * @method string getLabel()
 */
class Mage_Sales_Block_Adminhtml_Recurring_Profile_View_Tab_Info extends Mage_Adminhtml_Block_Widget implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Label getter
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('sales')->__('Profile Information');
    }

    /**
     * Also label getter :)
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getLabel();
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
}
