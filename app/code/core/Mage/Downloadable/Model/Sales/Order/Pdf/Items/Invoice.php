<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Downloadable
 */

/**
 * Order Invoice Downloadable Pdf Items renderer
 *
 * @package    Mage_Downloadable
 */
class Mage_Downloadable_Model_Sales_Order_Pdf_Items_Invoice extends Mage_Downloadable_Model_Sales_Order_Pdf_Items_Abstract
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
        $lines  = [];

        // draw Product name
        $lines[0] = [[
            'text' => Mage::helper('core/string')->str_split($item->getName(), 35, true, true),
            'feed' => 35,
        ]];

        // draw SKU
        $lines[0][] = [
            'text'  => Mage::helper('core/string')->str_split($this->getSku($item), 17),
            'feed'  => 290,
            'align' => 'right',
        ];

        // draw QTY
        $lines[0][] = [
            'text'  => $item->getQty() * 1,
            'feed'  => 435,
            'align' => 'right',
        ];

        // draw item Prices
        $i = 0;
        $prices = $this->getItemPricesForDisplay();
        $feedPrice = 395;
        $feedSubtotal = $feedPrice + 170;
        foreach ($prices as $priceData) {
            if (isset($priceData['label'])) {
                // draw Price label
                $lines[$i][] = [
                    'text'  => $priceData['label'],
                    'feed'  => $feedPrice,
                    'align' => 'right',
                ];
                // draw Subtotal label
                $lines[$i][] = [
                    'text'  => $priceData['label'],
                    'feed'  => $feedSubtotal,
                    'align' => 'right',
                ];
                $i++;
            }
            // draw Price
            $lines[$i][] = [
                'text'  => $priceData['price'],
                'feed'  => $feedPrice,
                'font'  => 'bold',
                'align' => 'right',
            ];
            // draw Subtotal
            $lines[$i][] = [
                'text'  => $priceData['subtotal'],
                'feed'  => $feedSubtotal,
                'font'  => 'bold',
                'align' => 'right',
            ];
            $i++;
        }

        // draw Tax
        $lines[0][] = [
            'text'  => $order->formatPriceTxt($item->getTaxAmount()),
            'feed'  => 495,
            'font'  => 'bold',
            'align' => 'right',
        ];

        // custom options
        $options = $this->getItemOptions();
        if ($options) {
            foreach ($options as $option) {
                // draw options label
                $lines[][] = [
                    'text' => Mage::helper('core/string')->str_split(strip_tags($option['label']), 40, true, true),
                    'font' => 'italic',
                    'feed' => 35,
                ];

                if ($option['value']) {
                    $printValue = $option['print_value'] ?? strip_tags($option['value']);
                    $values = explode(', ', $printValue);
                    foreach ($values as $value) {
                        $lines[][] = [
                            'text' => Mage::helper('core/string')->str_split($value, 30, true, true),
                            'feed' => 40,
                        ];
                    }
                }
            }
        }

        // downloadable Items
        $_purchasedItems = $this->getLinks()->getPurchasedItems();

        // draw Links title
        $lines[][] = [
            'text' => Mage::helper('core/string')->str_split($this->getLinksTitle(), 70, true, true),
            'font' => 'italic',
            'feed' => 35,
        ];

        // draw Links
        foreach ($_purchasedItems as $_link) {
            $lines[][] = [
                'text' => Mage::helper('core/string')->str_split($_link->getLinkTitle(), 50, true, true),
                'feed' => 40,
            ];
        }

        $lineBlock = [
            'lines'  => $lines,
            'height' => 20,
        ];

        $page = $pdf->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);
        $this->setPage($page);
    }
}
