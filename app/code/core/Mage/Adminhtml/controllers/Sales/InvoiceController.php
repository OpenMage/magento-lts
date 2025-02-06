<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml sales orders controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Sales_InvoiceController extends Mage_Adminhtml_Controller_Sales_Invoice
{
    /**
     * Export invoice grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName   = 'invoices.csv';
        $grid       = $this->getLayout()->createBlock('adminhtml/sales_invoice_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     *  Export invoice grid to Excel XML format
     */
    public function exportExcelAction()
    {
        $fileName   = 'invoices.xml';
        $grid       = $this->getLayout()->createBlock('adminhtml/sales_invoice_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
}
