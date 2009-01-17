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
class Mage_Bundle_Model_Sales_Order_Pdf_Items_Creditmemo extends Mage_Bundle_Model_Sales_Order_Pdf_Items_Abstract
{
    public function draw()
    {
        $order  = $this->getOrder();
        $item   = $this->getItem();
        $pdf    = $this->getPdf();
        $page   = $this->getPage();

        $items = $this->getChilds($item);
        $_prevOptionId = '';
        $shift  = array(0, 0, 0);
        $leftBound  =  35;
        $rightBound = 565;
        $size = 7;

        foreach ($items as $_item) {
            $this->_setFontRegular();
            $x = $leftBound;

            // draw selection attributes
            if ($_item->getOrderItem()->getParentItem()) {
                $attributes = $this->getSelectionAttributes($_item);
                if ($_prevOptionId != $attributes['option_id']) {
                    $this->_setfontItalic();
                    $page->drawText($attributes['option_label'], $x, $pdf->y, 'UTF-8');
                    $this->_setFontRegular();
                    $_prevOptionId = $attributes['option_id'];
                    $pdf->y -= 10;
                }
            }

            // draw product titles
            if ($_item->getOrderItem()->getParentItem()) {
                $feed = $x + 5;
                $name = $this->getValueHtml($_item);
            } else {
                $feed = $x;
                $name = $_item->getName();
            }
            foreach (Mage::helper('core/string')->str_split($name, 60, true, true) as $key => $part) {
                $page->drawText($part, $feed, $pdf->y - $shift[0], 'UTF-8');
                if ($key > 0) {
                    $shift[0] += 10;
                }
            }
            $x += 220;

            // draw SKUs
            $shift[2] = 0;
            if (!$_item->getOrderItem()->getParentItem()) {
                foreach (Mage::helper('core/string')->str_split($item->getSku(), 30) as $key => $part) {
                    if ($key > 0) {
                        $shift[2] += 10;
                    }
                    $page->drawText($part, $x, $pdf->y - $shift[2], 'UTF-8');
                }
            }
            $x += 100;

            $font = $this->_setFontBold();
            if ($this->canShowPriceInfo($_item)) {
                // draw Total(ex)
                $text = $order->formatPriceTxt($_item->getRowTotal());
                $page->drawText($text, $pdf->getAlignRight($text, $x, 50, $font, $size), $pdf->y, 'UTF-8');
                $x += 50;

                // draw Discount
                $text = $order->formatPriceTxt(-$_item->getDiscountAmount());
                $page->drawText($text, $pdf->getAlignRight($text, $x, 50, $font, $size), $pdf->y, 'UTF-8');
                $x += 50;

                // draw QTY
                $text = $_item->getQty() * 1;
                $page->drawText($text, $pdf->getAlignCenter($text, $x, 30, $font, $size), $pdf->y, 'UTF-8');
                $x += 30;

                // draw Tax
                $text = $order->formatPriceTxt($_item->getTaxAmount());
                $page->drawText($text, $pdf->getAlignRight($text, $x, 45, $font, $size, 10), $pdf->y, 'UTF-8');
                $x += 45;

                // draw Total(inc)
                $text = $order->formatPriceTxt($_item->getRowTotal()+$_item->getTaxAmount()-$_item->getDiscountAmount());
                $page->drawText($text, 565-$pdf->widthForStringUsingFontSize($text, $font, 7), $pdf->y, 'UTF-8');
            }

            $pdf->y -=max($shift)+10;
        }

        if ($item->getOrderItem()->getProductOptions() || $item->getOrderItem()->getDescription()) {
            $this->_setFontRegular();
            $options = $item->getOrderItem()->getProductOptions();
            if (isset($options['options'])) {
                foreach ($options['options'] as $option) {
                    $this->_setFontItalic();
                    foreach (Mage::helper('core/string')->str_split(strip_tags($option['label']), 60, false, true) as $_option) {
                        $page->drawText($_option, $leftBound, $pdf->y-$shift[1], 'UTF-8');
                        $shift[1] += 10;
                    }
                    $this->_setFontRegular();
                    if ($option['value']) {
                        $values = explode(', ', strip_tags($option['value']));
                        foreach ($values as $value) {
                            foreach (Mage::helper('core/string')->str_split($value, 50,true,true) as $_value) {
                                $page->drawText($_value, $leftBound + 5, $pdf->y-$shift[1], 'UTF-8');
                                $shift[1] += 10;
                            }
                        }
                    }
                }
            }

            foreach ($this->_parseDescription() as $description){
                $page->drawText(strip_tags($description), $leftBound + 5, $pdf->y-$shift{1}, 'UTF-8');
                $shift[1] += 10;
            }

            $pdf->y -= max($shift)+10;
        }
    }
}