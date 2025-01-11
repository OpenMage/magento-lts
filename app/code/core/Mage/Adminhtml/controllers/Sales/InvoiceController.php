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
