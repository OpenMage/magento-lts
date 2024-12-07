<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog product group price backend attribute model
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Resource_Product_Attribute_Backend_Groupprice extends Mage_Catalog_Model_Resource_Product_Attribute_Backend_Groupprice_Abstract
{
    protected function _construct()
    {
        $this->_init('catalog/product_attribute_group_price', 'value_id');
    }

    /**
     * Add is_percent column
     *
     * @param array $columns
     * @return array
     */
    protected function _loadPriceDataColumns($columns)
    {
        $columns               = parent::_loadPriceDataColumns($columns);
        $columns['is_percent'] = 'is_percent';
        return $columns;
    }
}
