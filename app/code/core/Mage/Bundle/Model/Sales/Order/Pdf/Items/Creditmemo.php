<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Bundle
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales Order Creditmemo Pdf default items renderer
 *
 * @category   Mage
 * @package    Mage_Bundle
 */
class Mage_Bundle_Model_Sales_Order_Pdf_Items_Creditmemo extends Mage_Bundle_Model_Sales_Order_Pdf_Items_Abstract
{
    /**
     * Draw item line
     *
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
            $x      = $leftBound;
            $line   = [];

            $attributes = $this->getSelectionAttributes($orderItem);
            if (is_array($attributes)) {
                $optionId   = $attributes['option_id'];
            } else {
                $optionId = 0;
            }

            if (!isset($drawItems[$optionId])) {
                $drawItems[$optionId] = [
                    'lines'  => [],
                    'height' => 15,
                ];
            }

            // draw selection attributes
            if ($orderItem->getOrderItem()->getParentItem()) {
                if ($_prevOptionId != $attributes['option_id']) {
                    $line[0] = [
                        'font'  => 'italic',
                        'text'  => Mage::helper('core/string')->str_split($attributes['option_label'], 38, true, true),
                        'feed'  => $x,
                    ];

                    $drawItems[$optionId] = [
                        'lines'  => [$line],
                        'height' => 15,
                    ];

                    $line = [];
                    $_prevOptionId = $attributes['option_id'];
                }
            }

            // draw product titles
            if ($orderItem->getOrderItem()->getParentItem()) {
                $feed = $x + 5;
                $name = $this->getValueHtml($orderItem);
            } else {
                $feed = $x;
                $name = $orderItem->getName();
            }

            $line[] = [
                'text'  => Mage::helper('core/string')->str_split($name, 35, true, true),
                'feed'  => $feed,
            ];

            $x += 220;

            // draw SKUs
            if (!$orderItem->getOrderItem()->getParentItem()) {
                $text = [];
                foreach (Mage::helper('core/string')->str_split($item->getSku(), 17) as $part) {
                    $text[] = $part;
                }
                $line[] = [
                    'text'  => $text,
                    'feed'  => $x,
                ];
            }

            $x += 100;

            // draw prices
            if ($this->canShowPriceInfo($orderItem)) {
                // draw Total(ex)
                $text = $order->formatPriceTxt($orderItem->getRowTotal());
                $line[] = [
                    'text'  => $text,
                    'feed'  => $x,
                    'font'  => 'bold',
                    'align' => 'right',
                    'width' => 50,
                ];
                $x += 50;

                // draw Discount
                $text = $order->formatPriceTxt(-$orderItem->getDiscountAmount());
                $line[] = [
                    'text'  => $text,
                    'feed'  => $x,
                    'font'  => 'bold',
                    'align' => 'right',
                    'width' => 50,
                ];
                $x += 50;

                // draw QTY
                $text = $orderItem->getQty() * 1;
                $line[] = [
                    'text'  => $orderItem->getQty() * 1,
                    'feed'  => $x,
                    'font'  => 'bold',
                    'align' => 'center',
                    'width' => 30,
                ];
                $x += 30;

                // draw Tax
                $text = $order->formatPriceTxt($orderItem->getTaxAmount());
                $line[] = [
                    'text'  => $text,
                    'feed'  => $x,
                    'font'  => 'bold',
                    'align' => 'right',
                    'width' => 45,
                ];
                $x += 45;

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
        if ($options) {
            if (isset($options['options'])) {
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
        }

        $page = $pdf->drawLineBlocks($page, $drawItems, ['table_header' => true]);
        $this->setPage($page);
    }
}
