<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Bundle
 */

/**
 * Sales Order Creditmemo Pdf default items renderer
 *
 * @package    Mage_Bundle
 */
class Mage_Bundle_Model_Sales_Order_Pdf_Items_Creditmemo extends Mage_Bundle_Model_Sales_Order_Pdf_Items_Abstract
{
    /**
     * @inheritDoc
     */
    public function draw()
    {
        $order  = $this->getOrder();
        $item   = $this->getItem();
        $pdf    = $this->getPdf();
        $page   = $this->getPage();

        $orderItems = $this->getChilds($item);
        $_prevOptionId = '';
        $drawItems  = [];
        $leftBound  = 35;
        $rightBound = 565;

        foreach ($orderItems as $orderItem) {
            $xAxis  = $leftBound;
            $line   = [];

            $attributes = $this->getSelectionAttributes($orderItem);
            $optionId = is_array($attributes) ? $attributes['option_id'] : 0;

            if (!isset($drawItems[$optionId])) {
                $drawItems[$optionId] = [
                    'lines'  => [],
                    'height' => 15,
                ];
            }

            // draw selection attributes
            if ($orderItem->getOrderItem()->getParentItem() && $_prevOptionId != $attributes['option_id']) {
                $line[0] = [
                    'font'  => 'italic',
                    'text'  => Mage::helper('core/string')->str_split($attributes['option_label'], 38, true, true),
                    'feed'  => $xAxis,
                ];
                $drawItems[$optionId] = [
                    'lines'  => [$line],
                    'height' => 15,
                ];
                $line = [];
                $_prevOptionId = $attributes['option_id'];
            }

            // draw product titles
            if ($orderItem->getOrderItem()->getParentItem()) {
                $feed = $xAxis + 5;
                $name = $this->getValueHtml($orderItem);
            } else {
                $feed = $xAxis;
                $name = $orderItem->getName();
            }

            $line[] = [
                'text'  => Mage::helper('core/string')->str_split($name, 35, true, true),
                'feed'  => $feed,
            ];

            $xAxis += 220;

            // draw SKUs
            if (!$orderItem->getOrderItem()->getParentItem()) {
                $text = [];
                foreach (Mage::helper('core/string')->str_split($item->getSku(), 17) as $part) {
                    $text[] = $part;
                }

                $line[] = [
                    'text'  => $text,
                    'feed'  => $xAxis,
                ];
            }

            $xAxis += 100;

            // draw prices
            if ($this->canShowPriceInfo($orderItem)) {
                // draw Total(ex)
                $text = $order->formatPriceTxt($orderItem->getRowTotal());
                $line[] = [
                    'text'  => $text,
                    'feed'  => $xAxis,
                    'font'  => 'bold',
                    'align' => 'right',
                    'width' => 50,
                ];
                $xAxis += 50;

                // draw Discount
                $text = $order->formatPriceTxt(-$orderItem->getDiscountAmount());
                $line[] = [
                    'text'  => $text,
                    'feed'  => $xAxis,
                    'font'  => 'bold',
                    'align' => 'right',
                    'width' => 50,
                ];
                $xAxis += 50;

                // draw QTY
                $text = $orderItem->getQty() * 1;
                $line[] = [
                    'text'  => $orderItem->getQty() * 1,
                    'feed'  => $xAxis,
                    'font'  => 'bold',
                    'align' => 'center',
                    'width' => 30,
                ];
                $xAxis += 30;

                // draw Tax
                $text = $order->formatPriceTxt($orderItem->getTaxAmount());
                $line[] = [
                    'text'  => $text,
                    'feed'  => $xAxis,
                    'font'  => 'bold',
                    'align' => 'right',
                    'width' => 45,
                ];
                $xAxis += 45;

                // draw Total(inc)
                $text = $order->formatPriceTxt(
                    $orderItem->getRowTotal() + $orderItem->getTaxAmount() - $orderItem->getDiscountAmount(),
                );
                $line[] = [
                    'text'  => $text,
                    'feed'  => $rightBound,
                    'font'  => 'bold',
                    'align' => 'right',
                ];
            }

            $drawItems[$optionId]['lines'][] = $line;
        }

        // custom options
        $options = $item->getOrderItem()->getProductOptions();
        if ($options && isset($options['options'])) {
            foreach ($options['options'] as $option) {
                $lines = [];
                $lines[][] = [
                    'text'  => Mage::helper('core/string')->str_split(strip_tags($option['label']), 40, true, true),
                    'font'  => 'italic',
                    'feed'  => $leftBound,
                ];

                if ($option['value']) {
                    $text = [];
                    $printValue = $option['print_value'] ?? strip_tags($option['value']);
                    $values = explode(', ', $printValue);
                    foreach ($values as $value) {
                        foreach (Mage::helper('core/string')->str_split($value, 30, true, true) as $str) {
                            $text[] = $str;
                        }
                    }

                    $lines[][] = [
                        'text'  => $text,
                        'feed'  => $leftBound + 5,
                    ];
                }

                $drawItems[] = [
                    'lines'  => $lines,
                    'height' => 15,
                ];
            }
        }

        $page = $pdf->drawLineBlocks($page, $drawItems, ['table_header' => true]);
        $this->setPage($page);
    }
}
