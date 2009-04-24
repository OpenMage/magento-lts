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
 * Sales Order Invoice Pdf default items renderer
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order_Pdf_Items_Invoice_Default extends Mage_Sales_Model_Order_Pdf_Items_Abstract
{
    public function draw()
    {
        $order  = $this->getOrder();
        $item   = $this->getItem();
        $pdf    = $this->getPdf();
        $page   = $this->getPage();
        $shift  = array(0, 10, 0);

        $this->_setFontRegular();

        $page->drawText($item->getQty()*1, 435, $pdf->y, 'UTF-8');

        /* in case Product name is longer than 80 chars - it is written in a few lines */
        foreach (Mage::helper('core/string')->str_split($item->getName(), 60, true, true) as $key => $part) {
            $page->drawText($part, 35, $pdf->y-$shift[0], 'UTF-8');
            $shift[0] += 10;
        }

        $options = $this->getItemOptions();
        if (isset($options)) {
            foreach ($options as $option) {
                // draw options label
                $this->_setFontItalic();
                foreach (Mage::helper('core/string')->str_split(strip_tags($option['label']), 60, false, true) as $_option) {
                    $page->drawText($_option, 35, $pdf->y-$shift[0], 'UTF-8');
                    $shift[0] += 10;
                }
                // draw options value
                $this->_setFontRegular();
                if ($option['value']) {
                    $_printValue = isset($option['print_value']) ? $option['print_value'] : strip_tags($option['value']);
                    $values = explode(', ', $_printValue);
                    foreach ($values as $value) {
                        foreach (Mage::helper('core/string')->str_split($value, 60,true,true) as $_value) {
                            $page->drawText($_value, 40, $pdf->y-$shift[0], 'UTF-8');
                            $shift[0] += 10;
                        }
                    }
                }
            }
        }

        foreach ($this->_parseDescription() as $description){
            $page->drawText(strip_tags($description), 65, $pdf->y-$shift[1], 'UTF-8');
            $shift[1] += 10;
        }

        /* in case Product SKU is longer than 36 chars - it is written in a few lines */
        foreach (Mage::helper('core/string')->str_split($this->getSku($item), 25) as $key => $part) {
            if ($key > 0) {
                $shift[2] += 10;
            }
            $page->drawText($part, 240, $pdf->y-$shift[2], 'UTF-8');
        }

        $font = $this->_setFontBold();

        $row_total = $order->formatPriceTxt($item->getRowTotal());
        $page->drawText($row_total, 565-$pdf->widthForStringUsingFontSize($row_total, $font, 7), $pdf->y, 'UTF-8');

        $price = $order->formatPriceTxt($item->getPrice());
        $page->drawText($price, 395-$pdf->widthForStringUsingFontSize($price, $font, 7), $pdf->y, 'UTF-8');

        $tax = $order->formatPriceTxt($item->getTaxAmount());
        $page->drawText($tax, 495-$pdf->widthForStringUsingFontSize($tax, $font, 7), $pdf->y, 'UTF-8');

        $pdf->y -= max($shift)+10;
    }
}