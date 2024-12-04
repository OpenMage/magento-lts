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
 * @copyright  Copyright (c) 2019-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml creditmemo items grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Creditmemo_Create_Items extends Mage_Adminhtml_Block_Sales_Items_Abstract
{
    public const BUTTON_SUBMIT_OFFLINE  = 'submit_offline';

    protected $_canReturnToStock;
    /**
     * Prepare child blocks
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->setChild(self::BUTTON_UPDATE, $this->getButtonUpdateBlock());

        if ($this->getCreditmemo()->canRefund()) {
            if ($this->getCreditmemo()->getInvoice() && $this->getCreditmemo()->getInvoice()->getTransactionId()) {
                $this->setChild(self::BUTTON_SUBMIT, $this->getButtonRefundBlock());
            }
            $this->setChild(self::BUTTON_SUBMIT_OFFLINE, $this->getButtonRefundOfflineBlock());
        } else {
            $this->setChild(self::BUTTON_SUBMIT, $this->getButtonRefundOfflineBlock());
        }

        return parent::_prepareLayout();
    }

    public function getButtonUpdateBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        $onclick = "submitAndReloadArea($('creditmemo_item_container'),'" . $this->getUpdateUrl() . "')";
        return parent::getButtonUpdateBlock($name, $attributes)
            ->setLabel(Mage::helper('sales')->__('Update Qty\'s'))
            ->setOnClick($onclick);
    }

    public function getButtonRefundBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonSaveBlock($name, $attributes)
            ->setLabel(Mage::helper('sales')->__('Refund'))
            ->setOnClick('disableElements(\'submit-button\');submitCreditMemo()')
            ->addClass('submit-button');
    }

    public function getButtonRefundOfflineBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonSaveBlock($name, $attributes)
            ->setLabel(Mage::helper('sales')->__('Refund Offline'))
            ->setOnClick('disableElements(\'submit-button\');submitCreditMemoOffline()')
            ->addClass('submit-button');
    }

    /**
     * Retrieve invoice order
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->getCreditmemo()->getOrder();
    }

    /**
     * Retrieve source
     *
     * @return Mage_Sales_Model_Order_Creditmemo
     */
    public function getSource()
    {
        return $this->getCreditmemo();
    }

    /**
     * Retrieve order totals block settings
     *
     * @return array
     */
    public function getOrderTotalData()
    {
        return [];
    }

    /**
     * Retrieve order totalbar block data
     *
     * @return array
     */
    public function getOrderTotalbarData()
    {
        $totalbarData = [];
        $this->setPriceDataObject($this->getOrder());
        $totalbarData[] = [Mage::helper('sales')->__('Paid Amount'), $this->displayPriceAttribute('total_invoiced'), false];
        $totalbarData[] = [Mage::helper('sales')->__('Refund Amount'), $this->displayPriceAttribute('total_refunded'), false];
        $totalbarData[] = [Mage::helper('sales')->__('Shipping Amount'), $this->displayPriceAttribute('shipping_invoiced'), false];
        $totalbarData[] = [Mage::helper('sales')->__('Shipping Refund'), $this->displayPriceAttribute('shipping_refunded'), false];
        $totalbarData[] = [Mage::helper('sales')->__('Order Grand Total'), $this->displayPriceAttribute('grand_total'), true];

        return $totalbarData;
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

    public function canEditQty()
    {
        if ($this->getCreditmemo()->getOrder()->getPayment()->canRefund()) {
            return $this->getCreditmemo()->getOrder()->getPayment()->canRefundPartialPerInvoice();
        }
        return true;
    }

    public function getUpdateUrl()
    {
        return $this->getUrl('*/*/updateQty', [
                'order_id' => $this->getCreditmemo()->getOrderId(),
                'invoice_id' => $this->getRequest()->getParam('invoice_id', null),
        ]);
    }

    public function canReturnToStock()
    {
        $canReturnToStock = Mage::getStoreConfig(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_CAN_SUBTRACT);
        if (Mage::getStoreConfig(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_CAN_SUBTRACT)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Whether to show 'Return to stock' column in creaditmemo grid
     * @return bool
     */
    public function canReturnItemsToStock()
    {
        if (is_null($this->_canReturnToStock)) {
            if ($this->_canReturnToStock = Mage::getStoreConfig(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_CAN_SUBTRACT)) {
                $canReturnToStock = false;
                foreach ($this->getCreditmemo()->getAllItems() as $item) {
                    $product = Mage::getModel('catalog/product')->load($item->getOrderItem()->getProductId());
                    if ($product->getId() && $product->getStockItem()->getManageStock()) {
                        $item->setCanReturnToStock($canReturnToStock = true);
                    } else {
                        $item->setCanReturnToStock(false);
                    }
                }
                $this->getCreditmemo()->getOrder()->setCanReturnToStock($this->_canReturnToStock = $canReturnToStock);
            }
        }
        return $this->_canReturnToStock;
    }

    public function canSendCreditmemoEmail()
    {
        return Mage::helper('sales')->canSendNewCreditmemoEmail($this->getOrder()->getStore()->getId());
    }
}
