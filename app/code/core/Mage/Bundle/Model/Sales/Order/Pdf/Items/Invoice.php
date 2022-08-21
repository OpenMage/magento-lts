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
 * @category    Mage
 * @package     Mage_Bundle
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
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
        $items = $this->getChilds($item);

        $_prevOptionId = '';
        $drawItems = [];

        /** @var Mage_Sales_Model_Order_Invoice_Item $_item */
        foreach ($items as $_item) {
            $line   = [];

            $attributes = $this->getSelectionAttributes($_item);
            if (is_array($attributes)) {
                $optionId = $attributes['option_id'];
            } else {
                $optionId = 0;
            }

            if (!isset($drawItems[$optionId])) {
                $drawItems[$optionId] = [
                    'lines'  => [],
                    'height' => 15
                ];
            }

            if ($_item->getOrderItem()->getParentItem()) {
                if ($_prevOptionId != $attributes['option_id']) {
                    $line[0] = [
                        'font' => 'italic',
                        'text' => $stringHelper->str_split($attributes['option_label'], 45, true, true),
                        'feed' => 35
                    ];

                    $drawItems[$optionId] = [
                        'lines'  => [$line],
                        'height' => 15
                    ];

                    $line = [];

                    $_prevOptionId = $attributes['option_id'];
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
            $line[] = [
                'text'  => $stringHelper->str_split($name, 35, true, true),
                'feed'  => $feed
            ];

            // draw SKUs
            if (!$_item->getOrderItem()->getParentItem()) {
                $text = [];
                foreach ($stringHelper->str_split($item->getSku(), 17) as $part) {
                    $text[] = $part;
                }
                $line[] = [
                    'text'  => $text,
                    'feed'  => 255
                ];
            }

            // draw prices
            if ($this->canShowPriceInfo($_item)) {
                if ($taxHelper->displaySalesPriceInclTax()) {
                    $price = $order->formatPriceTxt($_item->getPriceInclTax());
                } else {
                    $price = $order->formatPriceTxt($_item->getPrice());
                }
                $line[] = [
                    'text'  => $price,
                    'feed'  => 395,
                    'font'  => 'bold',
                    'align' => 'right'
                ];
                $line[] = [
                    'text'  => $_item->getQty()*1,
                    'feed'  => 435,
                    'font'  => 'bold',
                ];

                $tax = $order->formatPriceTxt($_item->getTaxAmount());
                $line[] = [
                    'text'  => $tax,
                    'feed'  => 495,
                    'font'  => 'bold',
                    'align' => 'right'
                ];

                if ($taxHelper->displaySalesPriceInclTax()) {
                    $row_total = $order->formatPriceTxt($_item->getRowTotalInclTax());
                } else {
                    $row_total = $order->formatPriceTxt($_item->getRowTotal());
                }
                $line[] = [
                    'text'  => $row_total,
                    'feed'  => 565,
                    'font'  => 'bold',
                    'align' => 'right'
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
                        'feed'  => 35
                    ];

                    if ($option['value']) {
                        $text = [];
                        $_printValue = isset($option['print_value'])
                            ? $option['print_value']
                            : strip_tags($option['value']);
                        $values = explode(', ', $_printValue);
                        foreach ($values as $value) {
                            foreach ($stringHelper->str_split($value, 30, true, true) as $_value) {
                                $text[] = $_value;
                            }
                        }

                        $lines[][] = [
                            'text'  => $text,
                            'feed'  => 40
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
