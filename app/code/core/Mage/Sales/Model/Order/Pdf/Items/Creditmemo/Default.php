<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Sales Order Creditmemo Pdf default items renderer
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Order_Pdf_Items_Creditmemo_Default extends Mage_Sales_Model_Order_Pdf_Items_Abstract
{
    /**
     * Draw process
     */
    public function draw()
    {
        $order  = $this->getOrder();
        $item   = $this->getItem();
        $pdf    = $this->getPdf();
        $page   = $this->getPage();
        $lines  = [];

        // draw Product name
        $lines[0] = [[
            'text' => Mage::helper('core/string')->str_split($item->getName(), 35, true, true),
            'feed' => 35,
        ]];

        // draw SKU
        $lines[0][] = [
            'text'  => Mage::helper('core/string')->str_split($this->getSku($item), 17),
            'feed'  => 255,
            'align' => 'right',
        ];

        // draw Total (ex)
        $lines[0][] = [
            'text'  => $order->formatPriceTxt($item->getRowTotal()),
            'feed'  => 330,
            'font'  => 'bold',
            'align' => 'right',
        ];

        // draw Discount
        $lines[0][] = [
            'text'  => $order->formatPriceTxt(-$item->getDiscountAmount()),
            'feed'  => 380,
            'font'  => 'bold',
            'align' => 'right',
        ];

        // draw QTY
        $lines[0][] = [
            'text'  => $item->getQty() * 1,
            'feed'  => 445,
            'font'  => 'bold',
            'align' => 'right',
        ];

        // draw Tax
        $lines[0][] = [
            'text'  => $order->formatPriceTxt($item->getTaxAmount()),
            'feed'  => 495,
            'font'  => 'bold',
            'align' => 'right',
        ];

        // draw Total (inc)
        $subtotal = $item->getRowTotal() + $item->getTaxAmount() + $item->getHiddenTaxAmount()
            - $item->getDiscountAmount();
        $lines[0][] = [
            'text'  => $order->formatPriceTxt($subtotal),
            'feed'  => 565,
            'font'  => 'bold',
            'align' => 'right',
        ];

        // draw options
        $options = $this->getItemOptions();
        if ($options) {
            foreach ($options as $option) {
                // draw options label
                $lines[][] = [
                    'text' => Mage::helper('core/string')->str_split(strip_tags($option['label']), 40, true, true),
                    'font' => 'italic',
                    'feed' => 35,
                ];

                // draw options value
                $printValue = $option['print_value'] ?? strip_tags($option['value']);
                $lines[][] = [
                    'text' => Mage::helper('core/string')->str_split($printValue, 30, true, true),
                    'feed' => 40,
                ];
            }
        }

        $lineBlock = [
            'lines'  => $lines,
            'height' => 20,
        ];

        $page = $pdf->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);
        $this->setPage($page);
    }
}
