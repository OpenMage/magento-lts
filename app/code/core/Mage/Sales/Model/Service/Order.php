<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Quote submit service model
 *
 * @package    Mage_Sales
 *
 * @method $this setEmailSent(bool $value)
 */
class Mage_Sales_Model_Service_Order
{
    /**
     * Order object
     *
     * @var Mage_Sales_Model_Order
     */
    protected $_order;

    /**
     * Quote convert object
     *
     * @var Mage_Sales_Model_Convert_Order
     */
    protected $_convertor;

    /**
     * Class constructor
     */
    public function __construct(Mage_Sales_Model_Order $order)
    {
        $this->_order       = $order;
        $this->_convertor   = Mage::getModel('sales/convert_order');
    }

    /**
     * Quote converter declaration
     *
     * @return  Mage_Sales_Model_Service_Order
     */
    public function setConvertor(Mage_Sales_Model_Convert_Order $convertor)
    {
        $this->_convertor = $convertor;
        return $this;
    }

    /**
     * Get assigned order object
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->_order;
    }

    /**
     * Updates numeric data taking into account locale
     *
     * @param array $data
     * @return $this
     */
    public function updateLocaleNumbers(&$data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_string($value) || is_numeric($value)) {
                    $data[$key] = $this->_getLocaleNumber($value);
                }
            }
        }
        return $this;
    }

    /**
     * Perform numbers conversion according to locale
     *
     * @param mixed $value
     * @return float
     */
    protected function _getLocaleNumber($value)
    {
        return Mage::app()->getLocale()->getNumber($value);
    }

    /**
     * Prepare order invoice based on order data and requested items qtys. If $qtys is not empty - the function will
     * prepare only specified items, otherwise all containing in the order.
     *
     * @param array $qtys
     * @return Mage_Sales_Model_Order_Invoice
     */
    public function prepareInvoice($qtys = [])
    {
        $this->updateLocaleNumbers($qtys);
        $invoice = $this->_convertor->toInvoice($this->_order);
        $totalQty = 0;
        foreach ($this->_order->getAllItems() as $orderItem) {
            if (!$this->_canInvoiceItem($orderItem, [])) {
                continue;
            }
            $item = $this->_convertor->itemToInvoiceItem($orderItem);
            if ($orderItem->isDummy()) {
                $qty = $orderItem->getQtyOrdered() ? $orderItem->getQtyOrdered() : 1;
            } elseif (isset($qtys[$orderItem->getId()])) {
                $qty = (float) $qtys[$orderItem->getId()];
            } elseif (!count($qtys)) {
                $qty = $orderItem->getQtyToInvoice();
            } else {
                $qty = 0;
            }

            $totalQty += $qty;
            $item->setQty($qty);
            $invoice->addItem($item);
        }

        $invoice->setTotalQty($totalQty);
        $invoice->collectTotals();
        $this->_order->getInvoiceCollection()->addItem($invoice);

        return $invoice;
    }

    /**
     * Prepare order shipment based on order items and requested items qty
     *
     * @param array $qtys
     * @return Mage_Sales_Model_Order_Shipment
     */
    public function prepareShipment($qtys = [])
    {
        $this->updateLocaleNumbers($qtys);
        $totalQty = 0;
        $shipment = $this->_convertor->toShipment($this->_order);
        foreach ($this->_order->getAllItems() as $orderItem) {
            if (!$this->_canShipItem($orderItem, $qtys)) {
                continue;
            }

            $item = $this->_convertor->itemToShipmentItem($orderItem);

            if ($orderItem->isDummy(true)) {
                $qty = 0;
                if (isset($qtys[$orderItem->getParentItemId()])) {
                    $productOptions = $orderItem->getProductOptions();
                    if (isset($productOptions['bundle_selection_attributes'])) {
                        $bundleSelectionAttributes = unserialize($productOptions['bundle_selection_attributes'], ['allowed_classes' => false]);

                        if ($bundleSelectionAttributes) {
                            $qty = $bundleSelectionAttributes['qty'] * $qtys[$orderItem->getParentItemId()];
                            $qty = min($qty, $orderItem->getSimpleQtyToShip());

                            $item->setQty($qty);
                            $shipment->addItem($item);
                            continue;
                        } else {
                            $qty = 1;
                        }
                    }
                } else {
                    $qty = 1;
                }
            } elseif (isset($qtys[$orderItem->getId()])) {
                $qty = min($qtys[$orderItem->getId()], $orderItem->getQtyToShip());
            } elseif (empty($qtys)) {
                $qty = $orderItem->getQtyToShip();
            } else {
                continue;
            }
            $totalQty += $qty;
            $item->setQty($qty);
            $shipment->addItem($item);
        }

        $shipment->setTotalQty($totalQty);
        return $shipment;
    }

    /**
     * Prepare order creditmemo based on order items and requested params
     *
     * @param array $data
     * @return Mage_Sales_Model_Order_Creditmemo
     */
    public function prepareCreditmemo($data = [])
    {
        $totalQty = 0;
        $creditmemo = $this->_convertor->toCreditmemo($this->_order);
        $qtys = $data['qtys'] ?? [];
        $this->updateLocaleNumbers($qtys);

        foreach ($this->_order->getAllItems() as $orderItem) {
            if (!$this->_canRefundItem($orderItem, $qtys)) {
                continue;
            }

            $item = $this->_convertor->itemToCreditmemoItem($orderItem);
            if ($orderItem->isDummy()) {
                $qty = 1;
                $orderItem->setLockedDoShip(true);
            } elseif (isset($qtys[$orderItem->getId()])) {
                $qty = (float) $qtys[$orderItem->getId()];
            } elseif (!count($qtys)) {
                $qty = $orderItem->getQtyToRefund();
            } else {
                continue;
            }
            $totalQty += $qty;
            $item->setQty($qty);
            $creditmemo->addItem($item);
        }
        $creditmemo->setTotalQty($totalQty);

        $this->_initCreditmemoData($creditmemo, $data);

        $creditmemo->collectTotals();
        return $creditmemo;
    }

    /**
     * Prepare order creditmemo based on invoice items and requested requested params
     *
     * @param Mage_Sales_Model_Order_Invoice $invoice
     * @param array $data
     * @return Mage_Sales_Model_Order_Creditmemo
     */
    public function prepareInvoiceCreditmemo($invoice, $data = [])
    {
        $totalQty = 0;
        $qtys = $data['qtys'] ?? [];
        $this->updateLocaleNumbers($qtys);

        $creditmemo = $this->_convertor->toCreditmemo($this->_order);
        $creditmemo->setInvoice($invoice);

        $invoiceQtysRefunded = [];
        foreach ($invoice->getOrder()->getCreditmemosCollection() as $createdCreditmemo) {
            if ($createdCreditmemo->getState() != Mage_Sales_Model_Order_Creditmemo::STATE_CANCELED
                && $createdCreditmemo->getInvoiceId() == $invoice->getId()
            ) {
                foreach ($createdCreditmemo->getAllItems() as $createdCreditmemoItem) {
                    $orderItemId = $createdCreditmemoItem->getOrderItem()->getId();
                    if (isset($invoiceQtysRefunded[$orderItemId])) {
                        $invoiceQtysRefunded[$orderItemId] += $createdCreditmemoItem->getQty();
                    } else {
                        $invoiceQtysRefunded[$orderItemId] = $createdCreditmemoItem->getQty();
                    }
                }
            }
        }

        $invoiceQtysRefundLimits = [];
        foreach ($invoice->getAllItems() as $invoiceItem) {
            $invoiceQtyCanBeRefunded = $invoiceItem->getQty();
            $orderItemId = $invoiceItem->getOrderItem()->getId();
            if (isset($invoiceQtysRefunded[$orderItemId])) {
                $invoiceQtyCanBeRefunded = $invoiceQtyCanBeRefunded - $invoiceQtysRefunded[$orderItemId];
            }
            $invoiceQtysRefundLimits[$orderItemId] = $invoiceQtyCanBeRefunded;
        }

        foreach ($invoice->getAllItems() as $invoiceItem) {
            $orderItem = $invoiceItem->getOrderItem();

            if (!$this->_canRefundItem($orderItem, $qtys, $invoiceQtysRefundLimits)) {
                continue;
            }

            $item = $this->_convertor->itemToCreditmemoItem($orderItem);
            if ($orderItem->isDummy()) {
                $qty = 1;
            } else {
                if (isset($qtys[$orderItem->getId()])) {
                    $qty = (float) $qtys[$orderItem->getId()];
                } elseif (!count($qtys)) {
                    $qty = $orderItem->getQtyToRefund();
                } else {
                    continue;
                }
                if (isset($invoiceQtysRefundLimits[$orderItem->getId()])) {
                    $qty = min($qty, $invoiceQtysRefundLimits[$orderItem->getId()]);
                }
            }
            $qty = min($qty, $invoiceItem->getQty());
            $totalQty += $qty;
            $item->setQty($qty);
            $creditmemo->addItem($item);
        }
        $creditmemo->setTotalQty($totalQty);

        $this->_initCreditmemoData($creditmemo, $data);
        if (!isset($data['shipping_amount'])) {
            $order = $invoice->getOrder();
            $isShippingInclTax = Mage::getSingleton('tax/config')->displaySalesShippingInclTax($order->getStoreId());
            if ($isShippingInclTax) {
                $baseAllowedAmount = $order->getBaseShippingInclTax()
                        - $order->getBaseShippingRefunded()
                        - $order->getBaseShippingTaxRefunded();
            } else {
                $baseAllowedAmount = $order->getBaseShippingAmount() - $order->getBaseShippingRefunded();
                $baseAllowedAmount = min($baseAllowedAmount, $invoice->getBaseShippingAmount());
            }
            $creditmemo->setBaseShippingAmount($baseAllowedAmount);
        }

        $creditmemo->collectTotals();
        return $creditmemo;
    }

    /**
     * Initialize creditmemo state based on requested parameters
     *
     * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
     * @param array $data
     */
    protected function _initCreditmemoData($creditmemo, $data)
    {
        $this->updateLocaleNumbers($data);
        if (isset($data['shipping_amount'])) {
            $creditmemo->setBaseShippingAmount((float) $data['shipping_amount']);
        }

        if (isset($data['adjustment_positive'])) {
            $creditmemo->setAdjustmentPositive($data['adjustment_positive']);
        }

        if (isset($data['adjustment_negative'])) {
            $creditmemo->setAdjustmentNegative($data['adjustment_negative']);
        }
    }

    /**
     * Check if order item can be invoiced. Dummy item can be invoiced or with his children or
     * with parent item which is included to invoice
     *
     * @param Mage_Sales_Model_Order_Item $item
     * @param array $qtys
     * @return bool
     */
    protected function _canInvoiceItem($item, $qtys = [])
    {
        if ($item->getLockedDoInvoice()) {
            return false;
        }
        $this->updateLocaleNumbers($qtys);

        if ($item->isDummy()) {
            if ($item->getHasChildren()) {
                foreach ($item->getChildrenItems() as $child) {
                    if (empty($qtys)) {
                        if ($child->getQtyToInvoice() > 0) {
                            return true;
                        }
                    } elseif (isset($qtys[$child->getId()]) && $qtys[$child->getId()] > 0) {
                        return true;
                    }
                }
                return false;
            } elseif ($item->getParentItem()) {
                $parent = $item->getParentItem();
                if (empty($qtys)) {
                    return $parent->getQtyToInvoice() > 0;
                } else {
                    return isset($qtys[$parent->getId()]) && $qtys[$parent->getId()] > 0;
                }
            }
        } else {
            return $item->getQtyToInvoice() > 0;
        }
        return false;
    }

    /**
     * Check if order item can be shipped. Dummy item can be shipped or with his children or
     * with parent item which is included to shipment
     *
     * @param Mage_Sales_Model_Order_Item $item
     * @param array $qtys
     * @return bool
     */
    protected function _canShipItem($item, $qtys = [])
    {
        if ($item->getIsVirtual() || $item->getLockedDoShip()) {
            return false;
        }
        $this->updateLocaleNumbers($qtys);

        if ($item->isDummy(true)) {
            if ($item->getHasChildren()) {
                if ($item->isShipSeparately()) {
                    return true;
                }
                foreach ($item->getChildrenItems() as $child) {
                    if ($child->getIsVirtual()) {
                        continue;
                    }
                    if (empty($qtys)) {
                        if ($child->getQtyToShip() > 0) {
                            return true;
                        }
                    } elseif (isset($qtys[$child->getId()]) && $qtys[$child->getId()] > 0) {
                        return true;
                    }
                }
                return false;
            } elseif ($item->getParentItem()) {
                $parent = $item->getParentItem();
                if (empty($qtys)) {
                    return $parent->getQtyToShip() > 0;
                } else {
                    return isset($qtys[$parent->getId()]) && $qtys[$parent->getId()] > 0;
                }
            }
        } else {
            return $item->getQtyToShip() > 0;
        }
        return false;
    }

    /**
     * Check if order item can be refunded
     *
     * @param Mage_Sales_Model_Order_Item $item
     * @param array $qtys
     * @param array $invoiceQtysRefundLimits
     * @return bool
     */
    protected function _canRefundItem($item, $qtys = [], $invoiceQtysRefundLimits = [])
    {
        $this->updateLocaleNumbers($qtys);
        if ($item->isDummy()) {
            if ($item->getHasChildren()) {
                foreach ($item->getChildrenItems() as $child) {
                    if (empty($qtys)) {
                        if ($this->_canRefundNoDummyItem($child, $invoiceQtysRefundLimits)) {
                            return true;
                        }
                    } elseif (isset($qtys[$child->getId()]) && $qtys[$child->getId()] > 0) {
                        return true;
                    }
                }
                return false;
            } elseif ($item->getParentItem()) {
                $parent = $item->getParentItem();
                if (empty($qtys)) {
                    return $this->_canRefundNoDummyItem($parent, $invoiceQtysRefundLimits);
                } else {
                    return isset($qtys[$parent->getId()]) && $qtys[$parent->getId()] > 0;
                }
            }
        } else {
            return $this->_canRefundNoDummyItem($item, $invoiceQtysRefundLimits);
        }
        return false;
    }

    /**
     * Check if no dummy order item can be refunded
     *
     * @param Mage_Sales_Model_Order_Item $item
     * @param array $invoiceQtysRefundLimits
     * @return bool
     */
    protected function _canRefundNoDummyItem($item, $invoiceQtysRefundLimits = [])
    {
        if ($item->getQtyToRefund() < 0) {
            return false;
        }

        if (isset($invoiceQtysRefundLimits[$item->getId()])) {
            return $invoiceQtysRefundLimits[$item->getId()] > 0;
        }

        return true;
    }
}
