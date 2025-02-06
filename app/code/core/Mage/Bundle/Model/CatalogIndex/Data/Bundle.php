<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Bundle
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
