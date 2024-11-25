<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Bundle
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Bundle product data retriever
 *
 * @category   Mage
 * @package    Mage_Bundle
 */
class Mage_Bundle_Model_CatalogIndex_Data_Bundle extends Mage_CatalogIndex_Model_Data_Simple
{
    /**
     * Defines when product type has children
     *
     * @var bool
     */
    protected $_haveChildren = [
        Mage_CatalogIndex_Model_Retreiver::CHILDREN_FOR_TIERS => false,
        Mage_CatalogIndex_Model_Retreiver::CHILDREN_FOR_PRICES => false,
        Mage_CatalogIndex_Model_Retreiver::CHILDREN_FOR_ATTRIBUTES => true,
    ];

    protected $_haveParents = false;

    /**
     * Retrieve product type code
     *
     * @return string
     */
    public function getTypeCode()
    {
        return Mage_Catalog_Model_Product_Type::TYPE_BUNDLE;
    }

    /**
     * Get child link table and field settings
     *
     * @return array
     */
    protected function _getLinkSettings()
    {
        return [
            'table' => 'bundle/selection',
            'parent_field' => 'parent_product_id',
            'child_field' => 'product_id',
        ];
    }

    /**
     * Prepare select statement before 'fetchLinkInformation' function result fetch
     *
     * @param int $store
     * @param string $table
     * @param string $idField
     * @param string $whereField
     * @param int $id
     * @param array $additionalWheres
     */
    protected function _prepareLinkFetchSelect($store, $table, $idField, $whereField, $id, $additionalWheres = [])
    {
        $this->_addAttributeFilter($this->_getLinkSelect(), 'required_options', 'l', $idField, $store, 0);
    }
}
