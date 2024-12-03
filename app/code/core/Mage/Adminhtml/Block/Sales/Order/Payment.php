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
 * Adminhtml sales order payment information
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Payment extends Mage_Adminhtml_Block_Template
{
    /**
     * Retrieve required options from parent
     */
    protected function _beforeToHtml()
    {
        if (!$this->getParentBlock()) {
            Mage::throwException(Mage::helper('adminhtml')->__('Invalid parent block for this block'));
        }
        $this->setPayment($this->getParentBlock()->getOrder()->getPayment());
        return parent::_beforeToHtml();
    }

    public function setPayment($payment)
    {
        $paymentInfoBlock = Mage::helper('payment')->getInfoBlock($payment);
        $this->setChild('info', $paymentInfoBlock);
        $this->setData('payment', $payment);
        return $this;
    }

    protected function _toHtml()
    {
        return $this->getChildHtml('info');
    }
}
