<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Bundle
 */

/**
 * Sales Order Invoice Pdf default items renderer
 *
 * @package    Mage_Bundle
 */
class Mage_Bundle_Model_Sales_Order_Pdf_Items_Invoice extends Mage_Bundle_Model_Sales_Order_Pdf_Items_Abstract
{
    /**
     * Draw item line
     */
    public function draw()
    {
        /** @var Mage_Tax_Helper_Data $taxHelper */
        $taxHelper = Mage::helper('tax');

        /** @var Mage_Core_Helper_String $stringHelper */
        $stringHelper = Mage::helper('core/string');

        $order  = $this->getOrder();

        /** @var Mage_Sales_Model_Order_Invoice_Item $item */
        $item   = $this->getItem();

        $pdf    = $this->getPdf();
        $page   = $this->getPage();

        $this->_setFontRegular();
        $orderItems = $this->getChilds($item);

        $_prevOptionId = '';
        $drawItems = [];

        /** @var Mage_Sales_Model_Order_Invoice_Item $orderItem */
        foreach ($orderItems as $orderItem) {
            $line   = [];

            $attributes = $this->getSelectionAttributes($orderItem);
            if (is_array($attributes)) {
                $optionId = $attributes['option_id'];
            } else {
                $optionId = 0;
            }

            if (!isset($drawItems[$optionId])) {
                $drawItems[$optionId] = [
                    'lines'  => [],
                    'height' => 15,
                ];
            }

            if ($orderItem->getOrderItem()->getParentItem()) {
                if ($_prevOptionId != $attributes['option_id']) {
                    $line[0] = [
                        'font' => 'italic',
                        'text' => $stringHelper->str_split($attributes['option_label'], 45, true, true),
                        'feed' => 35,
                    ];

                    $drawItems[$optionId] = [
                        'lines'  => [$line],
                        'height' => 15,
                    ];

                    $line = [];

                    $_prevOptionId = $attributes['option_id'];
                }
            }

            /* in case Product name is longer than 80 chars - it is written in a few lines */
            if ($orderItem->getOrderItem()->getParentItem()) {
                $feed = 40;
                $name = $this->getValueHtml($orderItem);
            } else {
                $feed = 35;
                $name = $orderItem->getName();
            }

            $line[] = [
                'text'  => $stringHelper->str_split($name, 35, true, true),
                'feed'  => $feed,
            ];

            // draw SKUs
            if (!$orderItem->getOrderItem()->getParentItem()) {
                $text = [];
                foreach ($stringHelper->str_split($item->getSku(), 17) as $part) {
                    $text[] = $part;
                }

                $line[] = [
                    'text'  => $text,
                    'feed'  => 255,
                ];
            }

            // draw prices
            if ($this->canShowPriceInfo($orderItem)) {
                if ($taxHelper->displaySalesPriceInclTax()) {
                    $price = $order->formatPriceTxt($orderItem->getPriceInclTax());
                } else {
                    $price = $order->formatPriceTxt($orderItem->getPrice());
                }

                $line[] = [
                    'text'  => $price,
                    'feed'  => 395,
                    'font'  => 'bold',
                    'align' => 'right',
                ];
                $line[] = [
                    'text'  => $orderItem->getQty() * 1,
                    'feed'  => 435,
                    'font'  => 'bold',
                ];

                $tax = $order->formatPriceTxt($orderItem->getTaxAmount());
                $line[] = [
                    'text'  => $tax,
                    'feed'  => 495,
                    'font'  => 'bold',
                    'align' => 'right',
                ];

                if ($taxHelper->displaySalesPriceInclTax()) {
                    $rowTotal = $order->formatPriceTxt($orderItem->getRowTotalInclTax());
                } else {
                    $rowTotal = $order->formatPriceTxt($orderItem->getRowTotal());
                }

                $line[] = [
                    'text'  => $rowTotal,
                    'feed'  => 565,
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
                        'text'  => $stringHelper->str_split(strip_tags($option['label']), 40, true, true),
                        'font'  => 'italic',
                        'feed'  => 35,
                    ];

                    if ($option['value']) {
                        $text = [];
                        $printValue = $option['print_value'] ?? strip_tags($option['value']);
                        $values = explode(', ', $printValue);
                        foreach ($values as $value) {
                            foreach ($stringHelper->str_split($value, 30, true, true) as $str) {
                                $text[] = $str;
                            }
                        }

                        $lines[][] = [
                            'text'  => $text,
                            'feed'  => 40,
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
