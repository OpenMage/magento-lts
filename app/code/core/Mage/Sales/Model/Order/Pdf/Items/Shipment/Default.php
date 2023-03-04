<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales Order Shipment Pdf default items renderer
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
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
            'feed'  => 35
        ];

        // draw SKU
        $lines[0][] = [
            'text'  => Mage::helper('core/string')->str_split($this->getSku($item), 25),
            'feed'  => 565,
            'align' => 'right'
        ];

        // Custom options
        $options = $this->getItemOptions();
        if ($options) {
            foreach ($options as $option) {
                // draw options label
                $lines[][] = [
                    'text' => Mage::helper('core/string')->str_split(strip_tags($option['label']), 70, true, true),
                    'font' => 'italic',
                    'feed' => 110
                ];

                // draw options value
                if ($option['value']) {
                    $_printValue = $option['print_value'] ?? strip_tags($option['value']);
                    $values = explode(', ', $_printValue);
                    foreach ($values as $value) {
                        $lines[][] = [
                            'text' => Mage::helper('core/string')->str_split($value, 50, true, true),
                            'feed' => 115
                        ];
                    }
                }
            }
        }

        $lineBlock = [
            'lines'  => $lines,
            'height' => 20
        ];

        $page = $pdf->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);
        $this->setPage($page);
    }
}
