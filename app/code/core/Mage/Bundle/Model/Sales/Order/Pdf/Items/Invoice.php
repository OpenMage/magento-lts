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
class Mage_Bundle_Model_Sales_Order_Pdf_Items_Invoice extends Mage_Bundle_Model_Sales_Order_Pdf_Items_Abstract
{
    public function draw()
    {
        $order  = $this->getOrder();
        $item   = $this->getItem();
        $pdf    = $this->getPdf();
        $page   = $this->getPage();

        $this->_setFontRegular();
        $items = $this->getChilds($item);

//                    $page->drawText(Mage::helper('sales')->__('Product'), 35, $this->y, 'UTF-8');
//                    $page->drawText(Mage::helper('sales')->__('SKU'), 240, $this->y, 'UTF-8');
//                    $page->drawText(Mage::helper('sales')->__('Price'), 380, $this->y, 'UTF-8');
//                    $page->drawText(Mage::helper('sales')->__('QTY'), 430, $this->y, 'UTF-8');
//                    $page->drawText(Mage::helper('sales')->__('Tax'), 480, $this->y, 'UTF-8');
//                    $page->drawText(Mage::helper('sales')->__('Subtotal'), 535, $this->y, 'UTF-8');

        $_prevOptionId = '';

        foreach ($items as $_item) {
            $shift  = array(0, 0, 0);

            $attributes = $this->getSelectionAttributes($_item);

            if ($_item->getOrderItem()->getParentItem()) {
                if ($_prevOptionId != $attributes['option_id']) {
                    $this->_setFontItalic();
                    $page->drawText($attributes['option_label'], 35, $pdf->y, 'UTF-8');
                    $this->_setFontRegular();
                    $_prevOptionId = $attributes['option_id'];
                    $pdf->y -= 10;
                }
            }

            /* in case Product name is longer than 80 chars - it is written in a few lines */
            if ($_item->getOrderItem()->getParentItem()) {
                $feed = 40;
                $name = $this->getValueHtml($_item);
            } else {
                $feed = 35;
                $name = $_item->getName();
            }
            foreach (Mage::helper('core/string')->str_split($name, 60, true, true) as $key => $part) {
                $page->drawText($part, $feed, $pdf->y-$shift[0], 'UTF-8');
                if ($key > 0) {
                    $shift[0] += 10;
                }
            }


            // draw SKUs
            if (!$_item->getOrderItem()->getParentItem()) {
                foreach (Mage::helper('core/string')->str_split($item->getSku(), 30) as $key => $part) {
                    if ($key > 0) {
                        $shift[2] += 10;
                    }
                    $page->drawText($part, 240, $pdf->y-$shift[2], 'UTF-8');
                }
            }

            // draw prices
            if ($this->canShowPriceInfo($_item)) {
                $font =  $this->_setFontBold();

                $price = $order->formatPriceTxt($_item->getPrice());
                $page->drawText($price, 395-$pdf->widthForStringUsingFontSize($price, $font, 7), $pdf->y, 'UTF-8');

                $page->drawText($_item->getQty()*1, 435, $pdf->y, 'UTF-8');

                $tax = $order->formatPriceTxt($_item->getTaxAmount());
                $page->drawText($tax, 495-$pdf->widthForStringUsingFontSize($tax, $font, 7), $pdf->y, 'UTF-8');

                $row_total = $order->formatPriceTxt($_item->getRowTotal());
                $page->drawText($row_total, 565-$pdf->widthForStringUsingFontSize($row_total, $font, 7), $pdf->y, 'UTF-8');
                $this->_setFontRegular();
            }

            $pdf->y -= max($shift)+10;
        }

        if ($item->getOrderItem()->getProductOptions() || $item->getOrderItem()->getDescription()) {
            $options = $item->getOrderItem()->getProductOptions();
            if (isset($options['options'])) {
                foreach ($options['options'] as $option) {
                    $this->_setFontItalic();
                    foreach (Mage::helper('core/string')->str_split(strip_tags($option['label']), 60,false, true) as $_option) {
                        $page->drawText($_option, 35, $pdf->y-$shift[1], 'UTF-8');
                        $shift[1] += 10;
                    }
                    $this->_setFontRegular();
                    if ($option['value']) {
                        $_printValue = isset($option['print_value']) ? $option['print_value'] : strip_tags($option['value']);
                        $values = explode(', ', $_printValue);
                        foreach ($values as $value) {
                            foreach (Mage::helper('core/string')->str_split($value, 70, true, true) as $_value) {
                                $page->drawText($_value, 40, $pdf->y-$shift[1], 'UTF-8');
                                $shift[1] += 10;
                            }
                        }
                    }
                }
            }

            foreach ($this->_parseDescription() as $description){
                $page->drawText(strip_tags($description), 65, $pdf->y-$shift{1}, 'UTF-8');
                $shift{1} += 10;
            }

            $pdf->y -= max($shift)+10;
        }
    }
}