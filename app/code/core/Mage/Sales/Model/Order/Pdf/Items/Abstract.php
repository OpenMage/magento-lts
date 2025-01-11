<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2017-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales Order Pdf Items renderer Abstract
 *
 * @category   Mage
 * @package    Mage_Sales
 */
abstract class Mage_Sales_Model_Order_Pdf_Items_Abstract extends Mage_Core_Model_Abstract
{
    /**
     * Order model
     *
     * @var Mage_Sales_Model_Order|null
     */
    protected $_order;

    /**
     * Source model (invoice, shipment, creditmemo)
     *
     * @var Mage_Core_Model_Abstract|null
     */
    protected $_source;

    /**
     * Item object
     *
     * @var Varien_Object|null
     */
    protected $_item;

    /**
     * Pdf object
     *
     * @var Mage_Sales_Model_Order_Pdf_Abstract|null
     */
    protected $_pdf;

    /**
     * Pdf current page
     *
     * @var Zend_Pdf_Page|null
     */
    protected $_pdfPage;

    /**
     * Set order model
     *
     * @return Mage_Sales_Model_Order_Pdf_Items_Abstract
     */
    public function setOrder(Mage_Sales_Model_Order $order)
    {
        $this->_order = $order;
        return $this;
    }

    /**
     * Set Source model
     *
     * @return Mage_Sales_Model_Order_Pdf_Items_Abstract
     */
    public function setSource(Mage_Core_Model_Abstract $source)
    {
        $this->_source = $source;
        return $this;
    }

    /**
     * Set item object
     *
     * @return Mage_Sales_Model_Order_Pdf_Items_Abstract
     */
    public function setItem(Varien_Object $item)
    {
        $this->_item = $item;
        return $this;
    }

    /**
     * Set Pdf model
     *
     * @return Mage_Sales_Model_Order_Pdf_Items_Abstract
     */
    public function setPdf(Mage_Sales_Model_Order_Pdf_Abstract $pdf)
    {
        $this->_pdf = $pdf;
        return $this;
    }

    /**
     * Set current page
     *
     * @return Mage_Sales_Model_Order_Pdf_Items_Abstract
     */
    public function setPage(Zend_Pdf_Page $page)
    {
        $this->_pdfPage = $page;
        return $this;
    }

    /**
     * Retrieve order object
     *
     * @throws Mage_Core_Exception
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if (is_null($this->_order)) {
            Mage::throwException(Mage::helper('sales')->__('Order object is not specified.'));
        }
        return $this->_order;
    }

    /**
     * Retrieve source object
     *
     * @throws Mage_Core_Exception
     * @return Mage_Core_Model_Abstract
     */
    public function getSource()
    {
        if (is_null($this->_source)) {
            Mage::throwException(Mage::helper('sales')->__('Source object is not specified.'));
        }
        return $this->_source;
    }

    /**
     * Retrieve item object
     *
     * @throws Mage_Core_Exception
     * @return Varien_Object
     */
    public function getItem()
    {
        if (is_null($this->_item)) {
            Mage::throwException(Mage::helper('sales')->__('Item object is not specified.'));
        }
        return $this->_item;
    }

    /**
     * Retrieve Pdf model
     *
     * @throws Mage_Core_Exception
     * @return Mage_Sales_Model_Order_Pdf_Abstract
     */
    public function getPdf()
    {
        if (is_null($this->_pdf)) {
            Mage::throwException(Mage::helper('sales')->__('PDF object is not specified.'));
        }
        return $this->_pdf;
    }

    /**
     * Retrieve Pdf page object
     *
     * @throws Mage_Core_Exception
     * @return Zend_Pdf_Page
     */
    public function getPage()
    {
        if (is_null($this->_pdfPage)) {
            Mage::throwException(Mage::helper('sales')->__('PDF page object is not specified.'));
        }
        return $this->_pdfPage;
    }

    /**
     * Draw item line
     *
     */
    abstract public function draw();

    /**
     * Format option value process
     *
     * @param  array|string $value
     * @return string
     */
    protected function _formatOptionValue($value)
    {
        $order = $this->getOrder();

        $resultValue = '';
        if (is_array($value)) {
            if (isset($value['qty'])) {
                $resultValue .= sprintf('%d', $value['qty']) . ' x ';
            }

            $resultValue .= $value['title'];

            if (isset($value['price'])) {
                $resultValue .= ' ' . $order->formatPrice($value['price']);
            }
            return  $resultValue;
        } else {
            return $value;
        }
    }

    /**
     * @deprecated To be Removed on next release
     *
     * @return array
     */
    protected function _parseDescription()
    {
        $description = $this->getItem()->getDescription();
        if (preg_match_all('/<li.*?>(.*?)<\/li>/i', $description, $matches)) {
            return $matches[1];
        }

        return [$description];
    }

    /**
     * Get array of arrays with item prices information for display in PDF
     * array(
     *  $index => array(
     *      'label'    => $label,
     *      'price'    => $price,
     *      'subtotal' => $subtotal
     *  )
     * )
     * @return array
     */
    public function getItemPricesForDisplay()
    {
        $order = $this->getOrder();
        $item  = $this->getItem();
        if (Mage::helper('tax')->displaySalesBothPrices()) {
            $prices = [
                [
                    'label'    => Mage::helper('tax')->__('Excl. Tax') . ':',
                    'price'    => $order->formatPriceTxt($item->getPrice()),
                    'subtotal' => $order->formatPriceTxt($item->getRowTotal()),
                ],
                [
                    'label'    => Mage::helper('tax')->__('Incl. Tax') . ':',
                    'price'    => $order->formatPriceTxt($item->getPriceInclTax()),
                    'subtotal' => $order->formatPriceTxt($item->getRowTotalInclTax()),
                ],
            ];
        } elseif (Mage::helper('tax')->displaySalesPriceInclTax()) {
            $prices = [[
                'price' => $order->formatPriceTxt($item->getPriceInclTax()),
                'subtotal' => $order->formatPriceTxt($item->getRowTotalInclTax()),
            ]];
        } else {
            $prices = [[
                'price' => $order->formatPriceTxt($item->getPrice()),
                'subtotal' => $order->formatPriceTxt($item->getRowTotal()),
            ]];
        }
        return $prices;
    }

    /**
     * Retrieve item options
     *
     * @return array
     */
    public function getItemOptions()
    {
        $result = [];
        if ($options = $this->getItem()->getOrderItem()->getProductOptions()) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
        }
        return $result;
    }

    /**
     * Set font as regular
     *
     * @param  int $size
     * @return Zend_Pdf_Resource_Font
     */
    protected function _setFontRegular($size = 7)
    {
        $font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
        $this->getPage()->setFont($font, $size);
        return $font;
    }

    /**
     * Set font as bold
     *
     * @param  int $size
     * @return Zend_Pdf_Resource_Font
     */
    protected function _setFontBold($size = 7)
    {
        $font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD);
        $this->getPage()->setFont($font, $size);
        return $font;
    }

    /**
     * Set font as italic
     *
     * @param  int $size
     * @return Zend_Pdf_Resource_Font
     */
    protected function _setFontItalic($size = 7)
    {
        $font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_OBLIQUE);
        $this->getPage()->setFont($font, $size);
        return $font;
    }

    /**
     * Return item Sku
     *
     * @param Mage_Sales_Model_Order_Invoice_Item|Mage_Sales_Model_Order_Creditmemo_Item $item
     * @return string
     */
    public function getSku($item)
    {
        if ($item->getOrderItem()->getProductOptionByCode('simple_sku')) {
            return $item->getOrderItem()->getProductOptionByCode('simple_sku');
        } else {
            return $item->getSku();
        }
    }
}
