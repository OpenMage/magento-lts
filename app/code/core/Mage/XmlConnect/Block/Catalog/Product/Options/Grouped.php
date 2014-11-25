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
 * Grouped product options xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Catalog_Product_Options_Grouped extends Mage_XmlConnect_Block_Catalog_Product_Options
{
    /**
     * Generate bundle product options xml
     *
     * @param Mage_Catalog_Model_Product $product
     * @param bool $isObject
     * @return string|Mage_XmlConnect_Model_Simplexml_Element
     */
    public function getProductOptionsXml(Mage_Catalog_Model_Product $product, $isObject = false)
    {
        $xmlModel = Mage::getModel('xmlconnect/simplexml_element', '<product></product>');
        $optionsNode = $xmlModel->addChild('options');

        if (!$product->getId()) {
            return $isObject ? $xmlModel : $xmlModel->asNiceXml();
        }
        $xmlModel->addAttribute('id', $product->getId());
        if (!$product->isSaleable()) {
            return $isObject ? $xmlModel : $xmlModel->asNiceXml();
        }
        /**
         * Grouped (associated) products
         */
        $associatedProducts = $product->getTypeInstance(true)->getAssociatedProducts($product);
        if (!sizeof($associatedProducts)) {
            return $isObject ? $xmlModel : $xmlModel->asNiceXml();
        }

        foreach ($associatedProducts as $item) {
            if (!$item->isSaleable()) {
                continue;
            }
            $optionNode = $optionsNode->addChild('option');

            $optionNode->addAttribute('code', 'super_group[' . $item->getId() . ']');
            $optionNode->addAttribute('type', 'product');
            $optionNode->addAttribute('label', $xmlModel->escapeXml($item->getName()));
            $optionNode->addAttribute('is_qty_editable', 1);
            $optionNode->addAttribute('qty', $item->getQty()*1);

            /**
             * Process product price
             */
            if ($item->getPrice() != $item->getFinalPrice()) {
                $productPrice = $item->getFinalPrice();
            } else {
                $productPrice = $item->getPrice();
            }

            if ($productPrice != 0) {
                $productPrice = Mage::helper('xmlconnect')->formatPriceForXml($productPrice);
                $optionNode->addAttribute('price', Mage::helper('xmlconnect')->formatPriceForXml(
                    Mage::helper('core')->currency($productPrice, false, false)
                ));
                $optionNode->addAttribute('formated_price', $this->_formatPriceString($productPrice, $product));
            }
        }

        return $isObject ? $xmlModel : $xmlModel->asNiceXml();
    }
}
