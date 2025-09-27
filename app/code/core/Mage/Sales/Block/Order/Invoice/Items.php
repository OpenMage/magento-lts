<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Sales order view items block
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Block_Order_Invoice_Items extends Mage_Sales_Block_Items_Abstract
{
    /**
     * Retrieve current order model instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('current_order');
    }

    /**
     * @param Mage_Sales_Model_Order_Invoice $invoice
     * @return string
     */
    public function getPrintInvoiceUrl($invoice)
    {
        return Mage::getUrl('*/*/printInvoice', ['invoice_id' => $invoice->getId()]);
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return string
     */
    public function getPrintAllInvoicesUrl($order)
    {
        return Mage::getUrl('*/*/printInvoice', ['order_id' => $order->getId()]);
    }

    /**
     * Get html of invoice totals block
     *
     * @param   Mage_Sales_Model_Order_Invoice $invoice
     * @return  string
     */
    public function getInvoiceTotalsHtml($invoice)
    {
        $html = '';
        $totals = $this->getChild('invoice_totals');
        if ($totals) {
            $totals->setInvoice($invoice);
            $html = $totals->toHtml();
        }
        return $html;
    }

    /**
     * Get html of invoice comments block
     *
     * @param   Mage_Sales_Model_Order_Invoice $invoice
     * @return  string
     */
    public function getInvoiceCommentsHtml($invoice)
    {
        $html = '';
        $comments = $this->getChild('invoice_comments');
        if ($comments) {
            $comments->setEntity($invoice)
                ->setTitle(Mage::helper('sales')->__('About Your Invoice'));
            $html = $comments->toHtml();
        }
        return $html;
    }
}
