<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Sales Order Shipment Pdf default items renderer
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Order_Pdf_Items_Shipment_Default extends Mage_Sales_Model_Order_Pdf_Items_Abstract
{
    /**
     * Draw item line
     */
    public function draw()
    {
        $item   = $this->getItem();
        $pdf    = $this->getPdf();
        $page   = $this->getPage();
        $lines  = [];

        // draw Product name
        $lines[0] = [[
            'text' => Mage::helper('core/string')->str_split($item->getName(), 60, true, true),
            'feed' => 100,
        ]];

        // draw QTY
        $lines[0][] = [
            'text'  => $item->getQty() * 1,
            'feed'  => 35,
        ];

        // draw SKU
        $lines[0][] = [
            'text'  => Mage::helper('core/string')->str_split($this->getSku($item), 25),
            'feed'  => 565,
            'align' => 'right',
        ];

        // Custom options
        $options = $this->getItemOptions();
        if ($options) {
            foreach ($options as $option) {
                // draw options label
                $lines[][] = [
                    'text' => Mage::helper('core/string')->str_split(strip_tags($option['label']), 70, true, true),
                    'font' => 'italic',
                    'feed' => 110,
                ];

                // draw options value
                if ($option['value']) {
                    $printValue = $option['print_value'] ?? strip_tags($option['value']);
                    $values = explode(', ', $printValue);
                    foreach ($values as $value) {
                        $lines[][] = [
                            'text' => Mage::helper('core/string')->str_split($value, 50, true, true),
                            'feed' => 115,
                        ];
                    }
                }
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
