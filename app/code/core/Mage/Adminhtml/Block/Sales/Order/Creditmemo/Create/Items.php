<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml creditmemo items grid
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Creditmemo_Create_Items extends Mage_Adminhtml_Block_Sales_Items_Abstract
{
    protected $_canReturnToStock;
    /**
     * Prepare child blocks
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $onclick = "submitAndReloadArea($('creditmemo_item_container'),'" . $this->getUpdateUrl() . "')";
        $this->setChild(
            'update_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')->setData([
                'label'     => Mage::helper('sales')->__('Update Qty\'s'),
                'class'     => 'update-button',
                'onclick'   => $onclick,
            ]),
        );

        if ($this->getCreditmemo()->canRefund()) {
            if ($this->getCreditmemo()->getInvoice() && $this->getCreditmemo()->getInvoice()->getTransactionId()) {
                $this->setChild(
                    'submit_button',
                    $this->getLayout()->createBlock('adminhtml/widget_button')->setData([
                        'label'     => Mage::helper('sales')->__('Refund'),
                        'class'     => 'save submit-button',
                        'onclick'   => 'disableElements(\'submit-button\');submitCreditMemo()',
                    ]),
                );
            }
            $this->setChild(
                'submit_offline',
                $this->getLayout()->createBlock('adminhtml/widget_button')->setData([
                    'label'     => Mage::helper('sales')->__('Refund Offline'),
                    'class'     => 'save submit-button',
                    'onclick'   => 'disableElements(\'submit-button\');submitCreditMemoOffline()',
                ]),
            );
        } else {
            $this->setChild(
                'submit_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')->setData([
                    'label'     => Mage::helper('sales')->__('Refund Offline'),
                    'class'     => 'save submit-button',
                    'onclick'   => 'disableElements(\'submit-button\');submitCreditMemoOffline()',
                ]),
            );
        }

        return parent::_prepareLayout();
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

    public function getUpdateButtonHtml()
    {
        return $this->getChildHtml('update_button');
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
