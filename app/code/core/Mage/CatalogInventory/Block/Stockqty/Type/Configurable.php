<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogInventory
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Product stock qty block for configurable product type
 *
 * @package    Mage_CatalogInventory
 */
class Mage_CatalogInventory_Block_Stockqty_Type_Configurable extends Mage_CatalogInventory_Block_Stockqty_Composite
{
    /**
     * Retrieve child products
     *
     * @return array
     */
    protected function _getChildProducts()
    {
        /** @var Mage_Catalog_Model_Product_Type_Configurable $productType */
        $productType = $this->_getProduct()->getTypeInstance(true);
        return $productType->getUsedProducts(null, $this->_getProduct());
    }
}
