<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
    public function draw()
    {
        $order  = $this->getOrder();
        $item   = $this->getItem();
        $pdf    = $this->getPdf();
        $page   = $this->getPage();
        $shift  = array(0, 10, 0);

        $this->_setFontRegular();
        $page->drawText($item->getQty()*1, 35, $pdf->y, 'UTF-8');

        foreach (Mage::helper('core/string')->str_split($item->getName(), 60, true, true) as $key => $part) {
            $page->drawText($part, 60, $pdf->y-$shift[0], 'UTF-8');
            $shift[0] += 10;
            /*if ($key > 0) {
                $shift[0] += 10;
            }*/
        }

        $options = $this->getItemOptions();
        if (isset($options)) {
            foreach ($options as $option) {
                // draw options label
                $this->_setFontItalic();
                foreach (Mage::helper('core/string')->str_split(strip_tags($option['label']), 60,false,true) as $_option) {
                    $page->drawText($_option, 60, $pdf->y-$shift[0], 'UTF-8');
                    $shift[0] += 10;
                }
                // draw options value
                $this->_setFontRegular();
                if ($option['value']) {
                    $values = explode(', ', strip_tags($option['value']));
                    foreach ($values as $value) {
                        foreach (Mage::helper('core/string')->str_split($value, 60,true,true) as $_value) {
                            $page->drawText($_value, 65, $pdf->y-$shift[0], 'UTF-8');
                            $shift[0] += 10;
                        }
                    }
                }
            }
        }

        foreach ($this->_parseDescription() as $description){
            $page->drawText(strip_tags($description), 65, $pdf->y-$shift{1}, 'UTF-8');
            $shift{1} += 10;
        }

        foreach (Mage::helper('core/string')->str_split($this->getSku($item), 25) as $key => $part) {
            if ($key > 0) {
                $shift[2] += 10;
            }
            $page->drawText($part, 440, $pdf->y-$shift[2], 'UTF-8');
        }

        $pdf->y -=max($shift)+10;
    }
}