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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright  Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product Options xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Catalog_Product_Options extends Mage_XmlConnect_Block_Catalog
{
     /**#@+
     * Option input type
     */
    const OPTION_TYPE_SELECT    = 'select';
    const OPTION_TYPE_CHECKBOX  = 'checkbox';
    const OPTION_TYPE_TEXT      = 'text';
    /**#@-*/

    /**
     * Store supported product options xml renderers based on product types
     *
     * @var array
     */
    protected $_renderers = array();

    /**
     * Add new product options renderer
     *
     * @param string $type
     * @param string $renderer
     * @return Mage_XmlConnect_Block_Product_Options
     */
    public function addRenderer($type, $renderer)
    {
        if (!isset($this->_renderers[$type])) {
            $this->_renderers[$type] = $renderer;
        }
        return $this;
    }

    /**
     * Create produc custom options Mage_XmlConnect_Model_Simplexml_Element object
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_XmlConnect_Model_Simplexml_Element
     */
    public function getProductCustomOptionsXmlObject(Mage_Catalog_Model_Product $product)
    {
        $xmlModel = Mage::getModel('xmlconnect/simplexml_element', '<product></product>');
        $optionsNode = $xmlModel->addChild('options');

        if ($product->hasPreconfiguredValues()) {
            $preConfiguredValues = $product->getPreconfiguredValues();
            $optionData = $preConfiguredValues->getData('options');
        }

        if (!$product->getId()) {
            return $xmlModel;
        }
        $xmlModel->addAttribute('id', $product->getId());
        if (!$product->isSaleable() || !sizeof($product->getOptions())) {
            return $xmlModel;
        }

        foreach ($product->getOptions() as $option) {
            $optionNode = $optionsNode->addChild('option');
            $type = $this->_getOptionTypeForXmlByRealType($option->getType());
            $code = 'options[' . $option->getId() . ']';
            if ($type == self::OPTION_TYPE_CHECKBOX) {
                $code .= '[]';
            }
            $optionNode->addAttribute('code', $code);
            $optionNode->addAttribute('type', $type);
            $optionNode->addAttribute('label', $xmlModel->escapeXml($option->getTitle()));
            if ($option->getIsRequire()) {
                $optionNode->addAttribute('is_required', 1);
            }

            /**
             * Process option price
             */
            $price = $option->getPrice();
            if ($price) {
                $optionNode->addAttribute('price', Mage::helper('xmlconnect')->formatPriceForXml($price));
                $formattedPrice = Mage::app()->getStore($product->getStoreId())->formatPrice($price, false);
                $optionNode->addAttribute('formated_price', $formattedPrice);
            }
            $optionId = $option->getOptionId();
            if ($type == self::OPTION_TYPE_CHECKBOX || $type == self::OPTION_TYPE_SELECT) {
                foreach ($option->getValues() as $value) {
                    $code = $value->getId();
                    $valueNode = $optionNode->addChild('value');
                    $valueNode->addAttribute('code', $code);
                    $valueNode->addAttribute('label', $xmlModel->escapeXml($value->getTitle()));

                    if ($value->getPrice() != 0) {
                        $price = Mage::helper('xmlconnect')->formatPriceForXml($value->getPrice());
                        $valueNode->addAttribute('price', $price);
                        $formattedPrice = $this->_formatPriceString($price, $product);
                        $valueNode->addAttribute('formated_price', $formattedPrice);
                    }
                    if ($product->hasPreconfiguredValues()) {
                        $this->_setCartSelectedValue($valueNode, $type, $this->_getPreconfiguredOption(
                            $optionData, $optionId, $code
                        ));
                    }
                }
            } else {
                if ($product->hasPreconfiguredValues() && array_key_exists($option->getOptionId(), $optionData)) {
                    $this->_setCartSelectedValue($optionNode, $type, $optionData[$optionId]);
                }
            }
        }
        return $xmlModel;
    }

    /**
     * Format price with currency code and taxes
     *
     * @param string|int|float $price
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    protected function _formatPriceString($price, $product)
    {
        $priceTax       = Mage::helper('tax')->getPrice($product, $price);
        $priceIncTax    = Mage::helper('tax')->getPrice($product, $price, true);

        if (Mage::helper('tax')->displayBothPrices() && $priceTax != $priceIncTax) {
            $formatted = Mage::helper('core')->currency($priceTax, true, false) . ' (+'
                . Mage::helper('core')->currency($priceIncTax, true, false) . ' '
                . Mage::helper('tax')->__('Incl. Tax') . ')';
        } else {
            $formatted = $this->helper('core')->currency($priceTax, true, false);
        }

        return $formatted;
    }

    /**
     * Retrieve option type name by specified option real type name
     *
     * @param string $realType
     * @return string
     */
    protected function _getOptionTypeForXmlByRealType($realType)
    {
        switch ($realType) {
            case Mage_Catalog_Model_Product_Option::OPTION_TYPE_DROP_DOWN:
            case Mage_Catalog_Model_Product_Option::OPTION_TYPE_RADIO:
                $type = self::OPTION_TYPE_SELECT;
                break;
            case Mage_Catalog_Model_Product_Option::OPTION_TYPE_MULTIPLE:
            case Mage_Catalog_Model_Product_Option::OPTION_TYPE_CHECKBOX:
                $type = self::OPTION_TYPE_CHECKBOX;
                break;
            case Mage_Catalog_Model_Product_Option::OPTION_TYPE_FIELD:
            case Mage_Catalog_Model_Product_Option::OPTION_TYPE_AREA:
            default:
                $type = self::OPTION_TYPE_TEXT;
                break;
        }
        return $type;
    }

    /**
     * Create product custom options Mage_XmlConnect_Model_Simplexml_Element object
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_XmlConnect_Model_Simplexml_Element | false
     */
    public function getProductOptionsXmlObject(Mage_Catalog_Model_Product $product)
    {
        if ($product->getId()) {
            $type = $product->getTypeId();
            if (isset($this->_renderers[$type])) {
                $renderer = $this->getLayout()->createBlock($this->_renderers[$type]);
                if ($renderer) {
                    return $renderer->getProductOptionsXml($product, true);
                }
            }
        }
        return false;
    }

    /**
     * Generate product options xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        $productId = $this->getRequest()->getParam('id', null);
        $product = Mage::getModel('catalog/product')->setStoreId(Mage::app()->getStore()->getId());

        if ($productId) {
            $product->load($productId);
        }

        if ($product->getId()) {
            Mage::register('product', $product);
            $type = $product->getTypeId();
            if (isset($this->_renderers[$type])) {
                $renderer = $this->getLayout()->createBlock($this->_renderers[$type]);
                if ($renderer) {
                    return $renderer->getProductOptionsXml($product);
                }
            }
        }
        return '<?xml version="1.0" encoding="UTF-8"?><options/>';
    }

    /**
     * Retrieve option type name by specified option real type name
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $xmlItem
     * @param string $type
     * @param int|null $value
     * @return Mage_XmlConnect_Block_Catalog_Product_Options
     */
    protected function _setCartSelectedValue($xmlItem, $type, $value = null)
    {
        if (empty($value)) {
            return $this;
        }

        switch ($type) {
            case Mage_Catalog_Model_Product_Option::OPTION_TYPE_DROP_DOWN:
            case Mage_Catalog_Model_Product_Option::OPTION_TYPE_RADIO:
            case Mage_Catalog_Model_Product_Option::OPTION_TYPE_MULTIPLE:
            case Mage_Catalog_Model_Product_Option::OPTION_GROUP_SELECT:
                $xmlItem->addAttribute('selected', 1);
                break;
            case Mage_Catalog_Model_Product_Option::OPTION_TYPE_CHECKBOX:
                $xmlItem->addAttribute('value', 1);
                break;
            case Mage_Catalog_Model_Product_Option::OPTION_TYPE_FIELD:
            case Mage_Catalog_Model_Product_Option::OPTION_TYPE_AREA:
            case Mage_Catalog_Model_Product_Option::OPTION_TYPE_FILE:
            case Mage_Catalog_Model_Product_Option::OPTION_TYPE_DATE:
            case Mage_Catalog_Model_Product_Option::OPTION_TYPE_TIME:
            case Mage_Catalog_Model_Product_Option::OPTION_TYPE_DATE_TIME:
            default:
                $xmlItem->addAttribute('value', $value);
                break;
        }
        return $this;
    }

    /**
     * Get preConfigured option value
     *
     * @param array $optionsData
     * @param int $optionId
     * @param int $valueId
     * @return int|null
     */
    protected function _getPreconfiguredOption($optionsData, $optionId, $valueId)
    {
        $optionValue = $optionsData[$optionId];
        if (is_array($optionValue)) {
            if (in_array($valueId, $optionValue)) {
                return $valueId;
            }
        }
        if ($valueId == $optionValue) {
            return $valueId;
        }

        return null;
    }
}


