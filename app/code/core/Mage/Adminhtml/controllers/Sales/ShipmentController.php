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
class Mage_Adminhtml_Sales_ShipmentController extends Mage_Adminhtml_Controller_Sales_Shipment
{
    /**
     * Export shipment grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName   = 'shipments.csv';
        $grid       = $this->getLayout()->createBlock('adminhtml/sales_shipment_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     *  Export shipment grid to Excel XML format
     */
    public function exportExcelAction()
    {
        $fileName   = 'shipments.xml';
        $grid       = $this->getLayout()->createBlock('adminhtml/sales_shipment_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
}
