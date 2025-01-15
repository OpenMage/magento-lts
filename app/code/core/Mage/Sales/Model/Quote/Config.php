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
 * @category   Mage
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
