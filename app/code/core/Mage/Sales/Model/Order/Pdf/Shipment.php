<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Sales Order Shipment PDF model
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Order_Pdf_Shipment extends Mage_Sales_Model_Order_Pdf_Abstract
{
    /**
     * Draw table header for product items
     */
    protected function _drawHeader(Zend_Pdf_Page $page)
    {
        /* Add table head */
        $this->_setFontRegular($page, 10);
        $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $this->y, 570, $this->y - 15);
        $this->y -= 10;
        $page->setFillColor(new Zend_Pdf_Color_Rgb(0, 0, 0));

        //columns headers
        $lines[0][] = [
            'text' => Mage::helper('sales')->__('Products'),
            'feed' => 100,
        ];

        $lines[0][] = [
            'text'  => Mage::helper('sales')->__('Qty'),
            'feed'  => 35,
        ];

        $lines[0][] = [
            'text'  => Mage::helper('sales')->__('SKU'),
            'feed'  => 565,
            'align' => 'right',
        ];

        $lineBlock = [
            'lines'  => $lines,
            'height' => 10,
        ];

        $this->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->y -= 20;
    }

    /**
     * Return PDF document
     *
     * @param  Mage_Sales_Model_Order_Shipment[] $shipments
     * @return Zend_Pdf
     */
    public function getPdf($shipments = [])
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('shipment');

        $pdf = new Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new Zend_Pdf_Style();
        $this->_setFontBold($style, 10);
        foreach ($shipments as $shipment) {
            if ($shipment->getStoreId()) {
                Mage::app()->getLocale()->emulate($shipment->getStoreId());
                Mage::app()->setCurrentStore($shipment->getStoreId());
            }
            $page  = $this->newPage();
            $order = $shipment->getOrder();
            /* Add image */
            $this->insertLogo($page, $shipment->getStore());
            /* Add address */
            $this->insertAddress($page, $shipment->getStore());
            /* Add head */
            $this->insertOrder(
                $page,
                $shipment,
                Mage::getStoreConfigFlag(self::XML_PATH_SALES_PDF_SHIPMENT_PUT_ORDER_ID, $order->getStoreId()),
            );
            /* Add document text and number */
            $this->insertDocumentNumber(
                $page,
                Mage::helper('sales')->__('Packingslip # ') . $shipment->getIncrementId(),
            );
            /* Add table */
            $this->_drawHeader($page);
            /* Add body */
            foreach ($shipment->getAllItems() as $item) {
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }
                /* Draw item */
                $this->_drawItem($item, $page, $order);
                $page = end($pdf->pages);
            }
        }
        $this->_afterGetPdf();
        if (isset($shipment) && $shipment->getStoreId()) {
            Mage::app()->getLocale()->revert();
        }
        return $pdf;
    }

    /**
     * Create new page and assign to PDF object
     *
     * @return Zend_Pdf_Page
     */
    public function newPage(array $settings = [])
    {
        /* Add new table head */
        $page = $this->_getPdf()->newPage(Zend_Pdf_Page::SIZE_A4);
        $this->_getPdf()->pages[] = $page;
        $this->y = 800;
        if (!empty($settings['table_header'])) {
            $this->_drawHeader($page);
        }
        return $page;
    }
}
