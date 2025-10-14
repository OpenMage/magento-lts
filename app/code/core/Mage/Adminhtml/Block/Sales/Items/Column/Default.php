<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml sales order column renderer
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Items_Column_Default extends Mage_Adminhtml_Block_Template
{
    public function getItem()
    {
        if ($this->_getData('item') instanceof Mage_Sales_Model_Order_Item) {
            return $this->_getData('item');
        } else {
            return $this->_getData('item')->getOrderItem();
        }
    }

    public function getOrderOptions()
    {
        $result = [];
        if ($options = $this->getItem()->getProductOptions()) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }

            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }

            if (!empty($options['attributes_info'])) {
                $result = array_merge($options['attributes_info'], $result);
            }
        }

        return $result;
    }

    /**
     * Return custom option html
     *
     * @param array $optionInfo
     * @return string
     */
    public function getCustomizedOptionValue($optionInfo)
    {
        // render customized option view
        $default = $optionInfo['value'];
        if (isset($optionInfo['option_type'])) {
            try {
                $group = Mage::getModel('catalog/product_option')->groupFactory($optionInfo['option_type']);
                return $group->getCustomizedView($optionInfo);
            } catch (Exception) {
                return $default;
            }
        }

        return $default;
    }

    public function getSku()
    {
        /*if ($this->getItem()->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
            return $this->getItem()->getProductOptionByCode('simple_sku');
        }*/
        return $this->getItem()->getSku();
    }
}
