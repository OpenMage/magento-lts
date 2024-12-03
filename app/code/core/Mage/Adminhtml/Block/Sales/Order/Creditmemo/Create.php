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
 * Adminhtml creditmemo create
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Creditmemo_Create extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'order_id';
        $this->_controller = 'sales_order_creditmemo';
        $this->_mode = 'create';

        parent::__construct();

        $this->_removeButton('delete');
        $this->_removeButton('save');
    }

    /**
     * Retrieve creditmemo model instance
     *
     * @return Mage_Sales_Model_Order_Creditmemo
     */
    public function getCreditmemo()
    {
        return Mage::registry('current_creditmemo');
    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->getCreditmemo()->getInvoice()) {
            $header = Mage::helper('sales')->__(
                'New Credit Memo for Invoice #%s',
                $this->escapeHtml($this->getCreditmemo()->getInvoice()->getIncrementId())
            );
        } else {
            $header = Mage::helper('sales')->__(
                'New Credit Memo for Order #%s',
                $this->escapeHtml($this->getCreditmemo()->getOrder()->getRealOrderId())
            );
        }

        return $header;
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/sales_order/view', ['order_id' => $this->getCreditmemo()->getOrderId()]);
    }
}
