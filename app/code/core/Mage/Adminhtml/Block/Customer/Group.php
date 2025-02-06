<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml customers group page content block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Customer_Group extends Mage_Adminhtml_Block_Widget_Grid_Container //Mage_Adminhtml_Block_Template
{
    /**
     * Modify header & button labels
     *
     */
    public function __construct()
    {
        $this->_controller = 'customer_group';
        $this->_headerText = Mage::helper('customer')->__('Customer Groups');
        $this->_addButtonLabel = Mage::helper('customer')->__('Add New Customer Group');
        parent::__construct();
    }

    /**
     * Redefine header css class
     *
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'icon-head head-customer-groups';
    }
}
