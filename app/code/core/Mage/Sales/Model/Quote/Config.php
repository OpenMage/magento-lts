<?php
/**
 * This file is part of OpenMage.
For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Quote_Config
{
    public const XML_PATH_QUOTE_PRODUCT_ATTRIBUTES = 'global/sales/quote/item/product_attributes';

    /**
     * @return array
     */
    public function getProductAttributes()
    {
        $attributes = Mage::getConfig()->getNode(self::XML_PATH_QUOTE_PRODUCT_ATTRIBUTES)->asArray();
        $transfer = new Varien_Object($attributes);
        Mage::dispatchEvent('sales_quote_config_get_product_attributes', ['attributes' => $transfer]);
        $attributes = $transfer->getData();
        return array_keys($attributes);
    }

    public function getTotalModels() {}
}
