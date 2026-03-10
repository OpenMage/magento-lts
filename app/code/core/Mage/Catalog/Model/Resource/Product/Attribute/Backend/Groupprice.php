<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog product group price backend attribute model
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Resource_Product_Attribute_Backend_Groupprice extends Mage_Catalog_Model_Resource_Product_Attribute_Backend_Groupprice_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('catalog/product_attribute_group_price', 'value_id');
    }

    /**
     * Add is_percent column
     *
     * @param  array $columns
     * @return array
     */
    protected function _loadPriceDataColumns($columns)
    {
        $columns               = parent::_loadPriceDataColumns($columns);
        $columns['is_percent'] = 'is_percent';
        return $columns;
    }
}
