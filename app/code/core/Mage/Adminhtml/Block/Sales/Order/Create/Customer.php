<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml sales order create block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Customer extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_create_customer');
    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('sales')->__('Please Select a Customer');
    }

    /**
     * @return string
     */
    public function getButtonsHtml()
    {
        $html = '';

        $addButtonData = [
            'label'     => Mage::helper('sales')->__('Create New Customer'),
            'onclick'   => 'order.setCustomerId(false)',
            'class'     => 'add',
        ];
        $html .= $this->getLayout()->createBlock('adminhtml/widget_button')->setData($addButtonData)->toHtml();

        $addButtonData = [
            'label'     => Mage::helper('sales')->__('Create Guest Order'),
            'onclick'   => 'order.setCustomerIsGuest()',
            'class'     => 'add',
        ];

        return $html . $this->getLayout()->createBlock('adminhtml/widget_button')->setData($addButtonData)->toHtml();
    }
}
