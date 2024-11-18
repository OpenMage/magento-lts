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
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Order item render block
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Block_Order_Item_Renderer_Default extends Mage_Core_Block_Template
{
    /**
     * @return $this
     */
    public function setItem(Varien_Object $item)
    {
        $this->setData('item', $item);
        return $this;
    }

    /**
     * @return mixed
     */
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

    /**
     * @return mixed
     */
    public function getOrderItem()
    {
        if ($this->getItem() instanceof Mage_Sales_Model_Order_Item) {
            return $this->getItem();
        } else {
            return $this->getItem()->getOrderItem();
        }
    }

    /**
     * @return array
     */
    public function getItemOptions()
    {
        $result = [];
        if ($options = $this->getOrderItem()->getProductOptions()) {
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
     * Accept option value and return its formatted view
     *
     * @param mixed $optionValue
     * Method works well with these $optionValue format:
     *      1. String
     *      2. Indexed array e.g. array(val1, val2, ...)
     *      3. Associative array, containing additional option info, including option value, e.g.
     *          array
     *          (
     *              [label] => ...,
     *              [value] => ...,
     *              [print_value] => ...,
     *              [option_id] => ...,
     *              [option_type] => ...,
     *              [custom_view] =>...,
     *          )
     *
     * @return array
     */
    public function getFormatedOptionValue($optionValue)
    {
        $optionInfo = [];

        // define input data format
        if (is_array($optionValue)) {
            if (isset($optionValue['option_id'])) {
                $optionInfo = $optionValue;
                if (isset($optionInfo['value'])) {
                    $optionValue = $optionInfo['value'];
                }
            } elseif (isset($optionValue['value'])) {
                $optionValue = $optionValue['value'];
            }
        }

        // render customized option view
        if (isset($optionInfo['custom_view']) && $optionInfo['custom_view']) {
            $_default = ['value' => $optionValue];
            if (isset($optionInfo['option_type'])) {
                try {
                    $group = Mage::getModel('catalog/product_option')->groupFactory($optionInfo['option_type']);
                    return ['value' => $group->getCustomizedView($optionInfo)];
                } catch (Exception $e) {
                    return $_default;
                }
            }
            return $_default;
        }

        // truncate standard view
        $result = [];
        if (is_array($optionValue)) {
            $truncatedValue = implode("\n", $optionValue);
            $truncatedValue = nl2br($truncatedValue);
            return ['value' => $truncatedValue];
        } else {
            $truncatedValue = Mage::helper('core/string')->truncate($optionValue, 55, '');
            $truncatedValue = nl2br($truncatedValue);
        }

        $result = ['value' => $truncatedValue];

        if (Mage::helper('core/string')->strlen($optionValue) > 55) {
            $result['value'] = $result['value'] . ' <a href="#" class="dots" onclick="return false">...</a>';
            $optionValue = nl2br($optionValue);
            $result = array_merge($result, ['full_view' => $optionValue]);
        }

        return $result;
    }

    /**
     * Return sku of order item.
     *
     * @return string
     */
    public function getSku()
    {
        /*if ($this->getOrderItem()->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
            return $this->getOrderItem()->getProductOptionByCode('simple_sku');
        }*/
        return $this->getItem()->getSku();
    }

    /**
     * Return product additional information block
     *
     * TODO set return type
     * @return Mage_Core_Block_Abstract|null
     */
    public function getProductAdditionalInformationBlock()
    {
        return $this->getLayout()->getBlock('additional.product.info');
    }
    public function canDisplayGiftmessage(): bool
    {
        if (!Mage::helper('core')->isModuleOutputEnabled('Mage_GiftMessage')) {
            return false;
        }
        /** @var Mage_GiftMessage_Helper_Message $helper */
        $helper = $this->helper('giftmessage/message');
        return $helper->getIsMessagesAvailable(
            $helper::TYPE_ORDER_ITEM,
            $this->getOrderItem()
        ) && $this->getItem()->getGiftMessageId();
    }

    public function getGiftMessage(): ?Mage_GiftMessage_Model_Message
    {
        if (!Mage::helper('core')->isModuleOutputEnabled('Mage_GiftMessage')) {
            return null;
        }
        /** @var Mage_GiftMessage_Helper_Message $helper */
        $helper = $this->helper('giftmessage/message');
        if ($this->getItem()->getGiftMessageId()) {
            return $helper->getGiftMessage($this->getItem()->getGiftMessageId());
        }
        return null;
    }
}
