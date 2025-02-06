<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Catalog
 */

/**
 * Catalog product group price backend attribute model
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Product_Attribute_Backend_Groupprice extends Mage_Catalog_Model_Product_Attribute_Backend_Groupprice_Abstract
{
    /**
     * Retrieve resource instance
     *
     * @return Mage_Catalog_Model_Resource_Product_Attribute_Backend_Groupprice
     */
    protected function _getResource()
    {
        return Mage::getResourceSingleton('catalog/product_attribute_backend_groupprice');
    }

    /**
     * Error message when duplicates
     *
     * @return string
     */
    protected function _getDuplicateErrorMessage()
    {
        return Mage::helper('catalog')->__('Duplicate website group price customer group.');
    }
}
