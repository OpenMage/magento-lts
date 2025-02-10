<?php
/**
 * Adminhtml additional helper block for product configuration
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_GiftMessage
 */
class Mage_GiftMessage_Block_Adminhtml_Product_Helper_Form_Config extends Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Config
{
    /**
     * Get config value data
     *
     * @return bool
     */
    protected function _getValueFromConfig()
    {
        return Mage::getStoreConfigFlag(Mage_GiftMessage_Helper_Message::XPATH_CONFIG_GIFT_MESSAGE_ALLOW_ITEMS);
    }
}
