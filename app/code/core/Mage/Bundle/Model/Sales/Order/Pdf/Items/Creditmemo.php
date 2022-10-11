<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Bundle
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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

        $items = $this->getChilds($item);
        $_prevOptionId = '';
        $drawItems  = [];
        $leftBound  = 35;
        $rightBound = 565;

        foreach ($items as $_item) {
            $x      = $leftBound;
            $line   = [];

            $attributes = $this->getSelectionAttributes($_item);
            if (is_array($attributes)) {
                $optionId   = $attributes['option_id'];
            } else {
                $optionId = 0;
            }

            if (!isset($drawItems[$optionId])) {
                $drawItems[$optionId] = [
                    'lines'  => [],
                    'height' => 15
                ];
            }

            // draw selection attributes
            if ($_item->getOrderItem()->getParentItem()) {
                if ($_prevOptionId != $attributes['option_id']) {
                    $line[0] = [
                        'font'  => 'italic',
                        'text'  => Mage::helper('core/string')->str_split($attributes['option_label'], 38, true, true),
                        'feed'  => $x
                    ];

                    $drawItems[$optionId] = [
                        'lines'  => [$line],
                        'height' => 15
                    ];

                    $line = [];
                    $_prevOptionId = $attributes['option_id'];
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

            $line[] = [
                'text'  => Mage::helper('core/string')->str_split($name, 35, true, true),
                'feed'  => $feed
            ];

            $x += 220;

            // draw SKUs
            if (!$_item->getOrderItem()->getParentItem()) {
                $text = [];
                foreach (Mage::helper('core/string')->str_split($item->getSku(), 17) as $part) {
                    $text[] = $part;
                }
                $line[] = [
                    'text'  => $text,
                    'feed'  => $x
                ];
            }

            $x += 100;

            // draw prices
            if ($this->canShowPriceInfo($_item)) {
                // draw Total(ex)
                $text = $order->formatPriceTxt($_item->getRowTotal());
                $line[] = [
                    'text'  => $text,
                    'feed'  => $x,
                    'font'  => 'bold',
                    'align' => 'right',
                    'width' => 50
                ];
                $x += 50;

                // draw Discount
                $text = $order->formatPriceTxt(-$_item->getDiscountAmount());
                $line[] = [
                    'text'  => $text,
                    'feed'  => $x,
                    'font'  => 'bold',
                    'align' => 'right',
                    'width' => 50
                ];
                $x += 50;

                // draw QTY
                $text = $_item->getQty() * 1;
                $line[] = [
                    'text'  => $_item->getQty()*1,
                    'feed'  => $x,
                    'font'  => 'bold',
                    'align' => 'center',
                    'width' => 30
                ];
                $x += 30;

                // draw Tax
                $text = $order->formatPriceTxt($_item->getTaxAmount());
                $line[] = [
                    'text'  => $text,
                    'feed'  => $x,
                    'font'  => 'bold',
                    'align' => 'right',
                    'width' => 45
                ];
                $x += 45;

                // draw Total(inc)
                $text = $order->formatPriceTxt(
                    $_item->getRowTotal() + $_item->getTaxAmount() - $_item->getDiscountAmount()
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
                        'feed'  => $leftBound
                    ];

                    if ($option['value']) {
                        $text = [];
                        $_printValue = isset($option['print_value'])
                            ? $option['print_value']
                            : strip_tags($option['value']);
                        $values = explode(', ', $_printValue);
                        foreach ($values as $value) {
                            foreach (Mage::helper('core/string')->str_split($value, 30, true, true) as $_value) {
                                $text[] = $_value;
                            }
                        }

                        $lines[][] = [
                            'text'  => $text,
                            'feed'  => $leftBound + 5
                        ];
                    }

                    $drawItems[] = [
                        'lines'  => $lines,
                        'height' => 15
                    ];
                }
            }
        }

        $page = $pdf->drawLineBlocks($page, $drawItems, ['table_header' => true]);
        $this->setPage($page);
    }
}
