<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogIndex
 */

/**
 * Configurable product data retriever
 *
 * @package    Mage_CatalogIndex
 */
class Mage_CatalogIndex_Model_Data_Configurable extends Mage_CatalogIndex_Model_Data_Abstract
{
    /**
     * Defines when product type has children
     *
     * @var int[]|bool[]
     */
    protected $_haveChildren = [
        Mage_CatalogIndex_Model_Retreiver::CHILDREN_FOR_TIERS => false,
        Mage_CatalogIndex_Model_Retreiver::CHILDREN_FOR_PRICES => false,
        Mage_CatalogIndex_Model_Retreiver::CHILDREN_FOR_ATTRIBUTES => true,
    ];

    /**
     * Defines when product type has parents
     *
     * @var bool
     */
    protected $_haveParents = false;

    protected function _construct()
    {
        $this->_init('catalogindex/data_configurable');
    }

    /**
      * Retrieve product type code
      *
      * @return string
      */
    public function getTypeCode()
    {
        return Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE;
    }

    /**
     * Get child link table and field settings
     *
     * @return mixed
     */
    protected function _getLinkSettings()
    {
        return [
            'table' => 'catalog/product_super_link',
            'parent_field' => 'parent_id',
            'child_field' => 'product_id',
        ];
    }
}
