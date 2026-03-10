<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Sales Order Pdf Items renderer Abstract
 *
 * @package    Mage_Sales
 */
abstract class Mage_Sales_Model_Order_Pdf_Items_Abstract extends Mage_Core_Model_Abstract
{
    /**
     * Order model
     *
     * @var null|Mage_Sales_Model_Order
     */
    protected $_order;

    /**
     * Source model (invoice, shipment, creditmemo)
     *
     * @var null|Mage_Core_Model_Abstract
     */
    protected $_source;

    /**
     * Item object
     *
     * @var null|Varien_Object
     */
    protected $_item;

    /**
     * Pdf object
     *
     * @var null|Mage_Sales_Model_Order_Pdf_Abstract
     */
    protected $_pdf;

    /**
     * Pdf current page
     *
     * @var null|Zend_Pdf_Page
     */
    protected $_pdfPage;

    /**
     * Set order model
     *
     * @return $this
     */
    public function setOrder(Mage_Sales_Model_Order $order)
    {
        $this->_order = $order;
        return $this;
    }

    /**
     * Set Source model
     *
     * @return $this
     */
    public function setSource(Mage_Core_Model_Abstract $source)
    {
        $this->_source = $source;
        return $this;
    }

    /**
     * Set item object
     *
     * @return $this
     */
    public function setItem(Varien_Object $item)
    {
        $this->_item = $item;
        return $this;
    }

    /**
     * Set Pdf model
     *
     * @return $this
     */
    public function setPdf(Mage_Sales_Model_Order_Pdf_Abstract $pdf)
    {
        $this->_pdf = $pdf;
        return $this;
    }

    /**
     * Set current page
     *
     * @return $this
     */
    public function setPage(Zend_Pdf_Page $page)
    {
        $this->_pdfPage = $page;
        return $this;
    }

    /**
     * Retrieve order object
     *
     * @return Mage_Sales_Model_Order
     * @throws Mage_Core_Exception
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
     * @return Mage_Core_Model_Abstract
     * @throws Mage_Core_Exception
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
     * @return Varien_Object
     * @throws Mage_Core_Exception
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
     * @return Mage_Sales_Model_Order_Pdf_Abstract
     * @throws Mage_Core_Exception
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
     * @return Zend_Pdf_Page
     * @throws Mage_Core_Exception
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
     */
    abstract public function draw();

    /**
     * Format option value process
     *
     * @param  array|string        $value
     * @return string
     * @throws Mage_Core_Exception
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
        }

        return $value;
    }

    /**
     * @return array
     * @throws Mage_Core_Exception
     * @deprecated To be Removed on next release
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
     * @throws Mage_Core_Exception
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
     * @throws Mage_Core_Exception
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
     * @param  int                    $size
     * @return Zend_Pdf_Resource_Font
     * @throws Mage_Core_Exception
     * @throws Zend_Pdf_Exception
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
     * @param  int                    $size
     * @return Zend_Pdf_Resource_Font
     * @throws Mage_Core_Exception
     * @throws Zend_Pdf_Exception
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
     * @param  int                    $size
     * @return Zend_Pdf_Resource_Font
     * @throws Mage_Core_Exception
     * @throws Zend_Pdf_Exception
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
     * @param  Mage_Sales_Model_Order_Creditmemo_Item|Mage_Sales_Model_Order_Invoice_Item|Varien_Object $item
     * @return string
     */
    public function getSku($item)
    {
        if ($item->getOrderItem()->getProductOptionByCode('simple_sku')) {
            return $item->getOrderItem()->getProductOptionByCode('simple_sku');
        }

        return $item->getSku();
    }
}
