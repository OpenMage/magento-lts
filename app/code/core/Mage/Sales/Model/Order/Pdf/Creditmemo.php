<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Sales Order Creditmemo PDF model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order_Pdf_Creditmemo extends Mage_Sales_Model_Order_Pdf_Abstract
{
    public function getPdf($creditmemos = array())
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('creditmemo');

        $pdf = new Zend_Pdf();
        $style = new Zend_Pdf_Style();
        $this->_setFontBold($style, 10);

        foreach ($creditmemos as $creditmemo) {
            $page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
            $pdf->pages[] = $page;

             $order = $creditmemo->getOrder();

            /* Add image */
            $this->insertLogo($page);

            /* Add address */
            $this->insertAddress($page);

            /* Add head */
            $this->insertOrder($page, $order, Mage::getStoreConfigFlag(self::XML_PATH_SALES_PDF_CREDITMEMO_PUT_ORDER_ID, $order->getStoreId()));

            $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
            $this->_setFontRegular($page);
            $page->drawText(Mage::helper('sales')->__('Credit Memo # ') . $creditmemo->getIncrementId(), 35, 780, 'UTF-8');

            /* Add table head */
            $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
            $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
            $page->setLineWidth(0.5);
            $page->drawRectangle(25, $this->y, 570, $this->y-15);
            $this->y -=10;
            $page->setFillColor(new Zend_Pdf_Color_RGB(0.4, 0.4, 0.4));
            $this->_drawHeader($page);
            $this->y -=15;

            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));

            /* Add body */
            foreach ($creditmemo->getAllItems() as $item){
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }

                $shift = array();
                if ($this->y<20) {
                    /* Add new table head */
                    $page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
                    $pdf->pages[] = $page;
                    $this->y = 800;

                    $this->_setFontRegular($page);
                    $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
                    $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
                    $page->setLineWidth(0.5);
                    $page->drawRectangle(25, $this->y, 570, $this->y-15);
                    $this->y -=10;
                    $this->_drawHeader($page);

                    $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
                    $this->y -=20;
                }

                /* Draw item */
                $this->_drawItem($item, $page, $order);
            }

            /* Add totals */
            $this->insertTotals($page, $creditmemo);
        }

        $this->_afterGetPdf();

        return $pdf;
    }

    protected function _drawHeader(Zend_Pdf_Page $page)
    {
        $font = $page->getFont();
        $size = $page->getFontSize();

        $page->drawText(Mage::helper('sales')->__('Products'), $x = 35, $this->y, 'UTF-8');
        $x += 220;

        $page->drawText(Mage::helper('sales')->__('SKU'), $x, $this->y, 'UTF-8');
        $x += 100;

        $text = Mage::helper('sales')->__('Total(ex)');
        $page->drawText($text, $this->getAlignRight($text, $x, 50, $font, $size), $this->y, 'UTF-8');
        $x += 50;

        $text = Mage::helper('sales')->__('Discount');
        $page->drawText($text, $this->getAlignRight($text, $x, 50, $font, $size), $this->y, 'UTF-8');
        $x += 50;

        $text = Mage::helper('sales')->__('QTY');
        $page->drawText($text, $this->getAlignCenter($text, $x, 30, $font, $size), $this->y, 'UTF-8');
        $x += 30;

        $text = Mage::helper('sales')->__('Tax');
        $page->drawText($text, $this->getAlignRight($text, $x, 45, $font, $size, 10), $this->y, 'UTF-8');
        $x += 45;

        $text = Mage::helper('sales')->__('Total(inc)');
        $page->drawText($text, $this->getAlignRight($text, $x, 570 - $x, $font, $size), $this->y, 'UTF-8');
    }
}