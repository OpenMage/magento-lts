<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Invoice API V2
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Order_Invoice_Api_V2 extends Mage_Sales_Model_Order_Invoice_Api
{
    /**
     * Create new invoice for order
     *
     * @param string $invoiceIncrementId
     * @param array $itemsQty
     * @param string $comment
     * @param bool $email
     * @param bool $includeComment
     * @return string
     */
    public function create($invoiceIncrementId, $itemsQty = [], $comment = null, $email = false, $includeComment = false)
    {
        $order = Mage::getModel('sales/order')->loadByIncrementId($invoiceIncrementId);
        $itemsQty = $this->_prepareItemQtyData($itemsQty);
        /** @var Mage_Sales_Model_Order $order */
        /**
          * Check order existing
          */
        if (!$order->getId()) {
            $this->_fault('order_not_exists');
        }

        /**
         * Check invoice create availability
         */
        if (!$order->canInvoice()) {
            $this->_fault('data_invalid', Mage::helper('sales')->__('Cannot do invoice for order.'));
        }

        $invoice = $order->prepareInvoice($itemsQty);

        $invoice->register();

        if ($comment !== null) {
            $invoice->addComment($comment, $email);
        }

        if ($email) {
            $invoice->setEmailSent(true);
        }

        $invoice->getOrder()->setIsInProcess(true);

        try {
            Mage::getModel('core/resource_transaction')->addObject($invoice)->addObject($invoice->getOrder())->save();
            $invoice->sendEmail($email, ($includeComment ? $comment : ''));
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        return $invoice->getIncrementId();
    }

    /**
     * Prepare items quantity data
     *
     * @param array $data
     * @return array
     */
    protected function _prepareItemQtyData($data)
    {
        $quantity = [];
        foreach ($data as $item) {
            if (isset($item->order_item_id) && isset($item->qty)) {
                $quantity[$item->order_item_id] = $item->qty;
            }
        }

        return $quantity;
    }
}
