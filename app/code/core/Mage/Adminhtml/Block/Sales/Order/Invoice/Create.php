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
 * Adminhtml invoice create
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Invoice_Create extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'order_id';
        $this->_controller = 'sales_order_invoice';
        $this->_mode = 'create';

        parent::__construct();

        $this->_removeButton('save');
        $this->_removeButton('delete');
    }

    /**
     * Retrieve invoice model instance
     *
     * @return Mage_Sales_Model_Order_Invoice
     */
    public function getInvoice()
    {
        return Mage::registry('current_invoice');
    }

    /**
     * Retrieve text for header
     *
     * @return string
     */
    public function getHeaderText()
    {
        return ($this->getInvoice()->getOrder()->getForcedDoShipmentWithInvoice())
            ? Mage::helper('sales')->__(
                'New Invoice and Shipment for Order #%s',
                $this->escapeHtml($this->getInvoice()->getOrder()->getRealOrderId())
            )
            : Mage::helper('sales')->__(
                'New Invoice for Order #%s',
                $this->escapeHtml($this->getInvoice()->getOrder()->getRealOrderId())
            );
    }

    /**
     * Retrieve back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/sales_order/view', ['order_id' => $this->getInvoice()->getOrderId()]);
    }
}
