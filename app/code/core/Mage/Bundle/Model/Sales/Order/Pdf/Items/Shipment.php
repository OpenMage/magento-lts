<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Bundle
 */

/**
 * Sales Order Shipment Pdf items renderer
 *
 * @package    Mage_Bundle
 */
class Mage_Bundle_Model_Sales_Order_Pdf_Items_Shipment extends Mage_Bundle_Model_Sales_Order_Pdf_Items_Abstract
{
    /**
     * Draw item line
     *
     */
    public function draw()
    {
        $item   = $this->getItem();
        $pdf    = $this->getPdf();
        $page   = $this->getPage();

        $this->_setFontRegular();

        $shipItems = $this->getChilds($item);
        $orderItems = array_merge([$item->getOrderItem()], $item->getOrderItem()->getChildrenItems());

        $_prevOptionId = '';
        $drawItems = [];

        foreach ($orderItems as $orderItem) {
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

            if ($orderItem->getParentItem()) {
                if ($_prevOptionId != $attributes['option_id']) {
                    $line[0] = [
                        'font'  => 'italic',
                        'text'  => Mage::helper('core/string')->str_split($attributes['option_label'], 60, true, true),
                        'feed'  => 60,
                    ];

                    $drawItems[$optionId] = [
                        'lines'  => [$line],
                        'height' => 15,
                    ];

                    $line = [];

                    $_prevOptionId = $attributes['option_id'];
                }
            }

            if (($this->isShipmentSeparately() && $orderItem->getParentItem())
                || (!$this->isShipmentSeparately() && !$orderItem->getParentItem())
            ) {
                if (isset($shipItems[$orderItem->getId()])) {
                    $qty = $shipItems[$orderItem->getId()]->getQty() * 1;
                } elseif ($orderItem->getIsVirtual()) {
                    $qty = Mage::helper('bundle')->__('N/A');
                } else {
                    $qty = 0;
                }
            } else {
                $qty = '';
            }

            $line[] = [
                'text'  => $qty,
                'feed'  => 35,
            ];

            // draw Name
            if ($orderItem->getParentItem()) {
                $feed = 65;
                $name = $this->getValueHtml($orderItem);
            } else {
                $feed = 60;
                $name = $orderItem->getName();
            }

            $text = [];
            foreach (Mage::helper('core/string')->str_split($name, 60, true, true) as $part) {
                $text[] = $part;
            }

            $line[] = [
                'text'  => $text,
                'feed'  => $feed,
            ];

            // draw SKUs
            $text = [];
            foreach (Mage::helper('core/string')->str_split($orderItem->getSku(), 25) as $part) {
                $text[] = $part;
            }

            $line[] = [
                'text'  => $text,
                'feed'  => 440,
            ];

            $drawItems[$optionId]['lines'][] = $line;
        }

        // custom options
        $options = $item->getOrderItem()->getProductOptions();
        if ($options) {
            if (isset($options['options'])) {
                foreach ($options['options'] as $option) {
                    $lines = [];
                    $lines[][] = [
                        'text'  => Mage::helper('core/string')->str_split(strip_tags($option['label']), 70, true, true),
                        'font'  => 'italic',
                        'feed'  => 60,
                    ];

                    if ($option['value']) {
                        $text = [];
                        $printValue = $option['print_value'] ?? strip_tags($option['value']);
                        $values = explode(', ', $printValue);
                        foreach ($values as $value) {
                            foreach (Mage::helper('core/string')->str_split($value, 50, true, true) as $str) {
                                $text[] = $str;
                            }
                        }

                        $lines[][] = [
                            'text'  => $text,
                            'feed'  => 65,
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
