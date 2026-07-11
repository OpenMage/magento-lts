<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Sales Order Email items default renderer
 *
 * @package    Mage_Sales
 * @method Mage_Sales_Model_Order_Item getItem()
 */
class Mage_Sales_Block_Order_Email_Items_Order_Default extends Mage_Core_Block_Template
{
    /**
     * Retrieve current order model instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->getItem()->getOrder();
    }

    /**
     * @return array
     */
    public function getItemOptions()
    {
        $result = [];
        if ($options = $this->getItem()->getProductOptions()) {
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
     * @param  array|string $value
     * @return string
     */
    public function getValueHtml($value)
    {
        if (is_array($value)) {
            return sprintf('%d', $value['qty']) . ' x ' . $this->escapeHtml($value['title']) . ' '
                . $this->getItem()->getOrder()->formatPrice($value['price']);
        }

        return $this->escapeHtml($value);
    }

    /**
     * @param  Mage_Sales_Model_Order_Item $item
     * @return array|string
     */
    public function getSku($item)
    {
        if ($item->getProductOptionByCode('simple_sku')) {
            return $item->getProductOptionByCode('simple_sku');
        }

        return $item->getSku();
    }

    /**
     * Return product additional information block
     *
     * TODO set return type
     * @return null|Mage_Core_Block_Abstract
     */
    public function getProductAdditionalInformationBlock()
    {
        return $this->getLayout()->getBlock('additional.product.info');
    }

    public function getGiftMessage(): ?Mage_GiftMessage_Model_Message
    {
        if (!$this->isModuleOutputEnabled('Mage_GiftMessage')) {
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
