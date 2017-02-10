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
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Configurable product options xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Catalog_Product_Options_Configurable extends Mage_XmlConnect_Block_Catalog_Product_Options
{
    /**
     * Generate bundle product options xml
     *
     * @param Mage_Catalog_Model_Product $product
     * @param bool $isObject
     * @return string | Mage_XmlConnect_Model_Simplexml_Element
     */
    public function getProductOptionsXml(Mage_Catalog_Model_Product $product, $isObject = false)
    {
        if ($product->hasPreconfiguredValues()) {
            $optionData = $product->getPreconfiguredValues()->getData('super_attribute');
        }

        $xmlModel = $this->getProductCustomOptionsXmlObject($product);
        $optionsXmlObj = $xmlModel->options;
        $options = array();

        if (!$product->isSaleable()) {
            return $isObject ? $xmlModel : $xmlModel->asNiceXml();
        }

        /**
         * Configurable attributes
         */
        $productAttributes = $product->getTypeInstance(true)->getConfigurableAttributes($product);
        if (!sizeof($productAttributes)) {
            return $isObject ? $xmlModel : $xmlModel->asNiceXml();
        }

        $allowProducts = array();
        $allProducts = $product->getTypeInstance(true)->getUsedProducts(null, $product);
        foreach ($allProducts as $productItem) {
            if ($productItem->isSaleable()) {
                $allowProducts[] = $productItem;
            }
        }

        /**
         * Allowed products options
         */
        foreach ($allowProducts as $item) {
            $productId  = $item->getId();

            foreach ($productAttributes as $attribute) {
                $productAttribute = $attribute->getProductAttribute();
                $attributeValue = $item->getData($productAttribute->getAttributeCode());
                if (!isset($options[$productAttribute->getId()])) {
                    $options[$productAttribute->getId()] = array();
                }

                if (!isset($options[$productAttribute->getId()][$attributeValue])) {
                    $options[$productAttribute->getId()][$attributeValue] = array();
                }
                $options[$productAttribute->getId()][$attributeValue][] = $productId;
            }
        }

        foreach ($productAttributes as $attribute) {
            $productAttribute = $attribute->getProductAttribute();
            $attributeId = $productAttribute->getId();
            $info = array(
               'id'        => $productAttribute->getId(),
               'label'     => $attribute->getLabel(),
               'options'   => array()
            );

            $prices = $attribute->getPrices();
            if (is_array($prices)) {
                foreach ($prices as $value) {
                    if (!isset($options[$attributeId][$value['value_index']])) {
                        continue;
                    }
                    $price = Mage::helper('xmlconnect')->formatPriceForXml(
                        $this->_preparePrice($product, $value['pricing_value'], $value['is_percent'])
                    );
                    $optionProducts = array();
                    if (isset($options[$attributeId][$value['value_index']])) {
                        $optionProducts = $options[$attributeId][$value['value_index']];
                    }
                    $info['options'][] = array(
                        'id'                => $value['value_index'],
                        'label'             => $value['label'],
                        'price'             => $price,
                        'formated_price'    => $this->_formatPriceString($price, $product),
                        'products'          => $optionProducts,
                    );
                }
            }

            if (sizeof($info['options']) > 0) {
               $attributes[$attributeId] = $info;
            }
        }

        $isFirst = true;

        $productAttributes = $attributes;
        reset($productAttributes);
        foreach ($attributes as $id => $attribute) {
            $optionNode = $optionsXmlObj->addChild('option');
            $optionNode->addAttribute('code', 'super_attribute[' . $id . ']');
            $optionNode->addAttribute('type', 'select');
            $optionNode->addAttribute('label', $optionsXmlObj->escapeXml($attribute['label']));
            $optionNode->addAttribute('is_required', 1);
            if ($isFirst) {
                foreach ($attribute['options'] as $option) {
                    $valueNode = $optionNode->addChild('value');
                    $valueNode->addAttribute('code', $option['id']);
                    $valueNode->addAttribute('label', $optionsXmlObj->escapeXml($option['label']));
                    if ((float)$option['price'] != 0.00) {
                        $valueNode->addAttribute('price', $option['price']);
                        $valueNode->addAttribute('formated_price', $option['formated_price']);
                    }
                    if (sizeof($productAttributes) > 1) {
                        $this->_prepareRecursivelyRelatedValues($valueNode, $productAttributes, $option['products'], 1);
                    }
                    if ($product->hasPreconfiguredValues()) {
                        $this->_setCartSelectedValue($valueNode, 'select', $this->_getPreconfiguredOption(
                            $optionData, $id, $option['id']
                        ));
                    }
                }
                $isFirst = false;
            }
        }

        return $isObject ? $xmlModel : $xmlModel->asNiceXml();
    }

    /**
     * Add recursively relations on each option
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element &$valueNode value node object
     * @param array $attributes all products attributes (options)
     * @param array $productIds prodcuts to search in next levels attributes
     * @param int $cycle
     */
    protected function _prepareRecursivelyRelatedValues(&$valueNode, $attributes, $productIds, $cycle = 1)
    {
        $relatedNode = null;

        for ($i = 0; $i < $cycle; $i++) {
            next($attributes);
        }
        $attribute = current($attributes);
        $attrId = key($attributes);
        foreach ($attribute['options'] as $option) {
            /**
             * Search products in option
             */
            $intersect = array_intersect($productIds, $option['products']);

            if (empty($intersect)) {
                continue;
            }

            if ($relatedNode === null) {
                $relatedNode = $valueNode->addChild('relation');
                $relatedNode->addAttribute('to', 'super_attribute[' . $attrId . ']');
            }

            $nodeValue = $relatedNode->addChild('value');
            $nodeValue->addAttribute('code', $option['id']);
            $nodeValue->addAttribute('label', $nodeValue->escapeXml($option['label']));
            if ((float)$option['price'] != 0.00) {
                $nodeValue->addAttribute('price', $option['price']);
                $nodeValue->addAttribute('formated_price', $option['formated_price']);
            }

            /**
             * Recursive relation adding
             */
            $attrClone = $attributes;
            if (next($attrClone) != false) {
                reset($attrClone);
                $this->_prepareRecursivelyRelatedValues($nodeValue, $attrClone, $intersect, $cycle + 1);
            }
        }
    }

    /**
     * Prepare price accordingly to percentage and store rates and round its
     *
     * @param Mage_Catalog_Model_Product $product
     * @param float|int|string $price
     * @param bool $isPercent
     * @return float
     */
    protected function _preparePrice($product, $price, $isPercent = false)
    {
        if ($isPercent && !empty($price)) {
            $price = $product->getFinalPrice() * $price / 100;
        }

        $price = Mage::app()->getStore()->convertPrice($price);
        $price = Mage::app()->getStore()->roundPrice($price);
        return $price;
    }
}
