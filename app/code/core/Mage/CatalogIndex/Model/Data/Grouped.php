<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogIndex
 */

/**
 * Grouped product data retriever
 *
 * @package    Mage_CatalogIndex
 */
class Mage_CatalogIndex_Model_Data_Grouped extends Mage_CatalogIndex_Model_Data_Abstract
{
    /**
     * Defines when product type has parents
     *
     * @var bool
     */
    protected $_haveParents = false;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('catalogindex/data_grouped');
    }

    /**
     * Fetch final price for product
     *
     * @param  array                     $product
     * @param  Mage_Core_Model_Store     $store
     * @param  Mage_Customer_Model_Group $group
     * @return false
     */
    public function getFinalPrice($product, $store, $group)
    {
        return false;
    }

    /**
     * Retrieve product type code
     *
     * @return string
     */
    public function getTypeCode()
    {
        return Mage_Catalog_Model_Product_Type::TYPE_GROUPED;
    }

    /**
     * Get child link table and field settings
     *
     * @return array
     */
    protected function _getLinkSettings()
    {
        return [
            'table' => 'catalog/product_link',
            'parent_field' => 'product_id',
            'child_field' => 'linked_product_id',
            'additional' => ['link_type_id' => Mage_Catalog_Model_Product_Link::LINK_TYPE_GROUPED],
        ];
    }
}
