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
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Sales Order Creditmemo Pdf default items renderer
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order_Pdf_Items_Creditmemo_Default extends Mage_Sales_Model_Order_Pdf_Items_Abstract
{
    public function draw()
    {
        $order  = $this->getOrder();
        $item   = $this->getItem();
        $pdf    = $this->getPdf();
        $page   = $this->getPage();
        $shift  = array(0, 10, 0);

        // draw quantity & name
        $this->_setFontRegular();
        $page->drawText($item->getQty()*1, 35, $pdf->y, 'UTF-8');
        foreach (Mage::helper('core/string')->str_split($item->getName(), 60, true, true) as $key => $part) {
            $page->drawText($part, 60, $pdf->y-$shift[0], 'UTF-8');
            $shift[0] += 10;
        }

        // draw options
        $options = $this->getItemOptions();
        if (isset($options)) {
            foreach ($options as $option) {
                // draw options label
                $this->_setFontItalic();
                foreach (Mage::helper('core/string')->str_split(strip_tags($option['label']), 60, false, true) as $_option) {
                    $page->drawText($_option, 60, $pdf->y-$shift[1], 'UTF-8');
                    $shift[1] += 10;
                }
                // draw options value
                $this->_setFontRegular();
                foreach (Mage::helper('core/string')->str_split(strip_tags($option['value']), 60, true, true) as $_value) {
                    $page->drawText($_value, 65, $pdf->y-$shift[1], 'UTF-8');
                    $shift[1] += 10;
                }
            }
        }

        // draw product description
        foreach ($this->_parseDescription() as $description){
            $page->drawText(strip_tags($description), 65, $pdf->y-$shift{1}, 'UTF-8');
            $shift{1} += 10;
        }

        // draw sku
        foreach (Mage::helper('core/string')->str_split($item->getSku(), 25) as $key => $part) {
            $page->drawText($part, 275, $pdf->y-$shift[2], 'UTF-8');
                $shift[2] += 10;
        }

        // draw amounts
        $font = $this->_setFontBold();
        $page->drawText($order->formatPriceTxt($item->getTaxAmount()), 380, $pdf->y, 'UTF-8');
        $page->drawText($order->formatPriceTxt(-$item->getDiscountAmount()), 430, $pdf->y, 'UTF-8');
        $page->drawText($order->formatPriceTxt($item->getRowTotal()), 480, $pdf->y, 'UTF-8');

        // draw total
        $rowTotal = $order->formatPriceTxt($item->getRowTotal()+$item->getTaxAmount()-$item->getDiscountAmount());
        $page->drawText($rowTotal, 565-$pdf->widthForStringUsingFontSize($rowTotal, $font, 7), $pdf->y, 'UTF-8');
        $pdf->y -=max($shift)+10;
    }
}