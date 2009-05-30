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
        $leftBound  =  35;
        $rightBound = 565;

        // draw name
//        $this->_setFontRegular();
        $x = $leftBound;
        // draw Product name
        $lines[0] = array(array(
            'text' => Mage::helper('core/string')->str_split($item->getName(), 60, true, true),
            'feed' => $x,
        ));
//        foreach (Mage::helper('core/string')->str_split($item->getName(), $x, true, true) as $key => $part) {
//            $page->drawText($part, $x, $pdf->y - $shift[0], 'UTF-8');
//            $shift[0] += 10;
//        }

        // draw options
        if ($options = $this->getItemOptions()) {
            foreach ($options as $option) {
                // draw options label
                // draw options label
                $lines[][] = array(
                    'text' => Mage::helper('core/string')->str_split(strip_tags($option['label']), 60, false, true),
                    'font' => 'italic',
                    'feed' => $x
                );
//
//                $this->_setFontItalic();
//                foreach (Mage::helper('core/string')->str_split(strip_tags($option['label']), $x, false, true) as $_option) {
//                    $page->drawText($_option, $x, $pdf->y - $shift[0], 'UTF-8');
//                    $shift[0] += 10;
//                }
                // draw options value

//                $this->_setFontRegular();
                $_printValue = isset($option['print_value']) ? $option['print_value'] : strip_tags($option['value']);
                $lines[][] = array(
                    'text' => Mage::helper('core/string')->str_split($_printValue, 55, true, true),
                    'feed' => $x + 5
                );
//                foreach (Mage::helper('core/string')->str_split($_printValue, $x, true, true) as $_value) {
//                    $page->drawText($_value, $x + 5, $pdf->y - $shift[0], 'UTF-8');
//                    $shift[0] += 10;
//                }
            }
        }

        $x += 220;

        // draw SKU
        $lines[0][] = array(
            'text'  => Mage::helper('core/string')->str_split($this->getSku($item), 25),
            'feed'  => $x
        );
//        foreach (Mage::helper('core/string')->str_split($this->getSku($item), 25) as $key => $part) {
//            $page->drawText($part, $x, $pdf->y - $shift[2], 'UTF-8');
//                $shift[2] += 10;
//        }
        $x += 100;

        // draw Total (ex)
        $lines[0][] = array(
            'text'  => $order->formatPriceTxt($item->getRowTotal()),
            'feed'  => $x,
            'font'  => 'bold',
            'align' => 'right',
            'width' => 50,
        );
        $x += 50;

        // draw Discount
        $lines[0][] = array(
            'text'  => $order->formatPriceTxt(-$item->getDiscountAmount()),
            'feed'  => $x,
            'font'  => 'bold',
            'align' => 'right',
            'width' => 50,
        );
        $x += 50;

        // draw QTY
        $lines[0][] = array(
            'text'  => $item->getQty()*1,
            'feed'  => $x,
            'font'  => 'bold',
            'align' => 'center',
            'width' => 30,
        );
        $x += 30;

        // draw Tax
        $lines[0][] = array(
            'text'  => $order->formatPriceTxt($item->getTaxAmount()),
            'feed'  => $x,
            'font'  => 'bold',
            'align' => 'right',
            'width' => 45,
        );
        $x += 45;

        // draw Subtotal
        $lines[0][] = array(
            'text'  => $order->formatPriceTxt($item->getRowTotal() + $item->getTaxAmount() - $item->getDiscountAmount()),
            'feed'  => $rightBound,
            'font'  => 'bold',
            'align' => 'right'
        );

//        $font = $this->_setFontBold();

        // draw Total(ex)
//        $text = $order->formatPriceTxt($item->getRowTotal());
//        $page->drawText($text, $pdf->getAlignRight($text, $x, 50, $font, 7), $pdf->y, 'UTF-8');
//        $x += 50;
//
//        // draw Discount
//        $text = $order->formatPriceTxt(-$item->getDiscountAmount());
//        $page->drawText($text, $pdf->getAlignRight($text, $x, 50, $font, 7), $pdf->y, 'UTF-8');
//        $x += 50;
//
//        // draw QTY
//        $text = $item->getQty() * 1;
//        $page->drawText($text, $pdf->getAlignCenter($text, $x, 30, $font, 7), $pdf->y, 'UTF-8');
//        $x += 30;
//
//        // draw Tax
//        $text = $order->formatPriceTxt($item->getTaxAmount());
//        $page->drawText($text, $pdf->getAlignRight($text, $x, 45, $font, 7, 10), $pdf->y, 'UTF-8');
//        $x += 45;
//
//        // draw Total(inc)
//        $text = $order->formatPriceTxt($item->getRowTotal() + $item->getTaxAmount() - $item->getDiscountAmount());
//        $page->drawText($text, $pdf->getAlignRight($text, $x, $rightBound - $x, $font, 7, 0), $pdf->y, 'UTF-8');
//
//        $pdf->y -= max($shift) + 10;
        $lineBlock = array(
            'lines'  => $lines,
            'height' => 10
        );

        $page = $pdf->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        $this->setPage($page);
    }
}