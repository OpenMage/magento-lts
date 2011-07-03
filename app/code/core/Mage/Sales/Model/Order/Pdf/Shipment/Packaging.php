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
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Sales Order Shipment PDF model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order_Pdf_Shipment_Packaging extends Mage_Sales_Model_Order_Pdf_Abstract
{
    /**
     * Format pdf file
     *
     * @param null $shipment
     * @return Zend_Pdf
     */
    public function getPdf($shipment = null)
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('shipment');

        $pdf = new Zend_Pdf();
        $page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
        $pdf->pages[] = $page;

        if ($shipment->getStoreId()) {
            Mage::app()->getLocale()->emulate($shipment->getStoreId());
            Mage::app()->setCurrentStore($shipment->getStoreId());
        }

        $this->_setFontRegular($page);
        $this->_drawHeaderBlock($page);

        $this->y = 740;
        $this->_drawPackageBlock($page);
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->_afterGetPdf();

        if ($shipment->getStoreId()) {
            Mage::app()->getLocale()->revert();
        }
        return $pdf;
    }

    /**
     * Draw header block
     *
     * @param  $page
     * @return Mage_Sales_Model_Order_Pdf_Shipment_Packaging
     */
    protected function _drawHeaderBlock($page) {
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, 790, 570, 755);
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
        $page->drawText(Mage::helper('sales')->__('Packages'), 35, 770, 'UTF-8');
        $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));

        return $this;
    }

    /**
     * Draw packages block
     *
     * @param  $page
     * @return Mage_Sales_Model_Order_Pdf_Shipment_Packaging
     */
    protected function _drawPackageBlock($page)
    {
        if ($this->getPackageShippingBlock()) {
            $packaging = $this->getPackageShippingBlock();
        } else {
            $packaging = Mage::getBlockSingleton('adminhtml/sales_order_shipment_packaging');
        }
        $packages = $packaging->getPackages();

        $i = 1;
        foreach ($packages as $packageId => $package) {
            $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
            $page->drawRectangle(25, $this->y + 15, 275, $this->y - 35);
            $page->drawRectangle(275, $this->y + 15, 570, $this->y - 35);

            $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
            $page->drawRectangle(520, $this->y + 15, 570, $this->y - 5);

            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
            $page->drawText('Package ' . $i, 525, $this->y , 'UTF-8');
            $i ++;

            $package = new Varien_Object($package);
            $params = new Varien_Object($package->getParams());
            $dimensionUnits = Mage::helper('usa')->getMeasureDimensionName($params->getDimensionUnits());

            $typeText = 'Type : ' . $params->getContainer();
            $lengthText = 'Length : ' . $params->getLength() .' '. $dimensionUnits;
            $widthText = 'Width : ' . $params->getWidth() .' '. $dimensionUnits;
            $heightText = 'Height : ' . $params->getHeight() .' '. $dimensionUnits;
            $weightText = 'Total Weight : ' . $params->getWeight() .' '
                        . Mage::helper('usa')->getMeasureWeightName($params->getWeightUnits());

            $page->drawText($typeText, 35, $this->y , 'UTF-8');
            $page->drawText($lengthText, 285, $this->y , 'UTF-8');

            $this->y = $this->y - 10;
            $page->drawText($weightText, 35, $this->y , 'UTF-8');
            $page->drawText($widthText, 285, $this->y , 'UTF-8');

            $this->y = $this->y - 10;
            $page->drawText($heightText, 285, $this->y , 'UTF-8');

            if ($sizeText = $this->_getSizeText($params)) {
                $page->drawText($sizeText, 35, $this->y , 'UTF-8');
                $this->y = $this->y - 10;
            }

            if ($params->getGirth()) {
                $dimensionGirthUnits = Mage::helper('usa')->getMeasureDimensionName($params->getGirthDimensionUnits());
                $girthText = 'Girth : ' . $params->getGirth() .' '. $dimensionGirthUnits;
                $page->drawText($girthText, 285, $this->y , 'UTF-8');
                $this->y = $this->y - 10;
            }

            $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
            $page->drawRectangle(25, $this->y+5, 570, $this->y - 25);

            $this->y = $this->y - 10;
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
            $page->drawText('Items in the Package', 50, $this->y, 'UTF-8');

            $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
            $page->drawRectangle(50, $this->y - 5, 200, $this->y - 15);
            $page->drawRectangle(200, $this->y - 5, 350, $this->y - 15);
            $page->drawRectangle(350, $this->y - 5, 500, $this->y - 15);

            $this->y = $this->y - 12;
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
            $page->drawText('Product', 55, $this->y, 'UTF-8');
            $page->drawText('Weight', 205, $this->y, 'UTF-8');
            $page->drawText('Qty', 355, $this->y, 'UTF-8');

            foreach ($package->getItems() as $itemId => $item) {
                $item = new Varien_Object($item);

                $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
                $page->drawRectangle(50, $this->y - 3, 200, $this->y - 15);
                $page->drawRectangle(200, $this->y - 3, 350, $this->y - 15);
                $page->drawRectangle(350, $this->y - 3, 500, $this->y - 15);

                $this->y = $this->y - 12;
                $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
                $page->drawText($item->getName(), 55, $this->y, 'UTF-8');
                $page->drawText($item->getWeight(), 205, $this->y, 'UTF-8');
                $page->drawText($item->getQty()*1, 355, $this->y, 'UTF-8');

            }
                $this->y = $this->y - 30;
        }
        return $this;
    }

    /**
     * Get package size from params either from system config
     *
     * @param  $params
     * @return string
     */
    protected function _getSizeText($params)
    {
        $uspsModel = Mage::getSingleton('usa/shipping_carrier_usps');
        if ($params->getSize()) {
            $sizeText = 'Size : ' . $params->getSize();
        } else {
            $sizeText = 'Size : ' . $uspsModel->getConfigData('size');
        }
        return $sizeText;
    }

}
