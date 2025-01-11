<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
