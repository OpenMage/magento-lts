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
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Bundle product options xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Catalog_Product_Options_Bundle extends Mage_XmlConnect_Block_Catalog_Product_Options
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
        $xmlModel = $this->getProductCustomOptionsXmlObject($product);
        $optionsXmlObj = $xmlModel->options;

        if (!$product->isSaleable()) {
            return $isObject ? $xmlModel : $xmlModel->asNiceXml();
        }

        if ($product->hasPreconfiguredValues()) {
            $optionData = $product->getPreconfiguredValues()->getData('bundle_option');
        }

        /**
         * Bundle options
         */
        $product->getTypeInstance(true)->setStoreFilter($product->getStoreId(), $product);
        $optionCollection = $product->getTypeInstance(true)->getOptionsCollection($product);
        $selectionCollection = $product->getTypeInstance(true)->getSelectionsCollection(
            $product->getTypeInstance(true)->getOptionsIds($product), $product
        );
        $bundleOptions = $optionCollection->appendSelections($selectionCollection, false, false);
        if (!sizeof($bundleOptions)) {
            return $isObject ? $xmlModel : $xmlModel->asNiceXml();
        }

        foreach ($bundleOptions as $option) {
            $selections = $option->getSelections();
            $optionId = $option->getOptionId();
            if (empty($selections)) {
                continue;
            }

            $optionNode = $optionsXmlObj->addChild('option');

            $type = parent::OPTION_TYPE_SELECT;
            if ($option->isMultiSelection()) {
                $type = parent::OPTION_TYPE_CHECKBOX;
            }
            $code = 'bundle_option[' . $option->getId() . ']';
            if ($type == parent::OPTION_TYPE_CHECKBOX) {
                $code .= '[]';
            }
            $optionNode->addAttribute('code', $code);
            $optionNode->addAttribute('type', $type);
            $optionNode->addAttribute('label', $optionsXmlObj->escapeXml($option->getTitle()));
            if ($option->getRequired()) {
                $optionNode->addAttribute('is_required', 1);
            }

            foreach ($selections as $selection) {
                if (!$selection->isSaleable()) {
                    continue;
                }
                $qty = null;
                if ($product->hasPreconfiguredValues()) {
                    $qty = $product->getPreconfiguredValues()->getData("bundle_option_qty/{$optionId}");
                }
                if (null === $qty) {
                    $qty = !($selection->getSelectionQty() * 1) ? '1' : $selection->getSelectionQty() * 1;
                }

                $valueNode = $optionNode->addChild('value');
                $valueNode->addAttribute('code', $selection->getSelectionId());
                $valueNode->addAttribute('label', $optionsXmlObj->escapeXml($selection->getName()));
                if (!$option->isMultiSelection()) {
                    if ($selection->getSelectionCanChangeQty()) {
                        $valueNode->addAttribute('is_qty_editable', 1);
                    }
                }
                $valueNode->addAttribute('qty', $qty);

                $price = $product->getPriceModel()->getSelectionPreFinalPrice($product, $selection);
                $price = Mage::helper('xmlconnect')->formatPriceForXml($price);
                if ((float)$price != 0.00) {
                    $valueNode->addAttribute('price', Mage::helper('xmlconnect')->formatPriceForXml(
                        Mage::helper('core')->currency($price, false, false)
                    ));
                    $valueNode->addAttribute('formated_price', $this->_formatPriceString($price, $product));
                }

                if ($product->hasPreconfiguredValues()) {
                    $this->_setCartSelectedValue($valueNode, $type, $this->_getPreconfiguredOption(
                        $optionData, $optionId, $selection->getSelectionId()
                    ));
                }
            }
        }
        return $isObject ? $xmlModel : $xmlModel->asNiceXml();
    }
}
