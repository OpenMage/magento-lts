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
 * @copyright   Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Order item render block
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Block_Order_Item_Renderer_Default extends Mage_Core_Block_Template
{
    public function setItem(Varien_Object $item)
    {
        $this->setData('item', $item);
        return $this;
    }

    public function getItem()
    {
        return $this->_getData('item');
    }

    /**
     * Retrieve current order model instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->getOrderItem()->getOrder();
    }


    public function getOrderItem()
    {
        if ($this->getItem() instanceof Mage_Sales_Model_Order_Item) {
            return $this->getItem();
        } else {
            return $this->getItem()->getOrderItem();
        }
    }

    public function getItemOptions()
    {
        $result = array();

        if ($options = $this->getOrderItem()->getProductOptions()) {
            if (isset($options['options'])) {
                /**
                 * Remove html tags from option
                 */
                $productOptions = $options['options'];
                if ($this->getPrintStatus()) {
                    foreach ($productOptions as &$option) {
                    	if (isset($option['value'])) {
                            $option['value'] = strip_tags($option['value']);
                    	}
                    }
                }
                $result = array_merge($result, $productOptions);
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

    public function getFormatedOptionValue($optionValue)
    {
        if (Mage::helper('catalog/product_options')->isHtmlFormattedOptionValue($optionValue)) {
            return array('value' => $optionValue);
        }

        $formateOptionValue = array();
        if (is_array($optionValue)) {
            $_truncatedValue = implode("\n", $optionValue);
            $_truncatedValue = nl2br($_truncatedValue);
            return array('value' => $_truncatedValue);
        } else {
            $_truncatedValue = Mage::helper('core/string')->truncate($optionValue, 55, '');
            $_truncatedValue = nl2br($_truncatedValue);
        }

        $formateOptionValue = array(
            'value' => $_truncatedValue
        );

        if (Mage::helper('core/string')->strlen($optionValue) > 55) {
            $formateOptionValue['value'] = $formateOptionValue['value'] . ' <a href="#" class="dots" onclick="return false">...</a>';
            $optionValue = nl2br($optionValue);
            $formateOptionValue = array_merge($formateOptionValue, array('full_view' => $optionValue));
        }

        return $formateOptionValue;
    }

    /**
     * Return sku of order item.
     *
     * @return string
     */
    public function getSku()
    {
        if ($this->getOrderItem()->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
            return $this->getOrderItem()->getProductOptionByCode('simple_sku');
        }
        return $this->getItem()->getSku();
    }
}