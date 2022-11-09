<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Quote_Config
{
    const XML_PATH_QUOTE_PRODUCT_ATTRIBUTES = 'global/sales/quote/item/product_attributes';

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

    public function getTotalModels()
    {
    }
}
