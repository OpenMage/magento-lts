<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_CatalogIndex
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Grouped product data retreiver
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogIndex_Model_Data_Grouped extends Mage_CatalogIndex_Model_Data_Abstract
{
    /**
     * Defines when product type has parents
     *
     * @var boolean
     */
    protected $_haveParents = false;

    protected function _construct()
    {
        $this->_init('catalogindex/data_grouped');
    }

    /**
     * Fetch final price for product
     *
     * @param int $product
     * @param Mage_Core_Model_Store $store
     * @param Mage_Customer_Model_Group $group
     * @return float
     */
    public function getFinalPrice($product, $store, $group)
    {
        return false;
    }

    /**
     * Retreive product type code
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
     * @return mixed
     */
    protected function _getLinkSettings()
    {
        return array(
                    'table'=>'catalog/product_link',
                    'parent_field'=>'product_id',
                    'child_field'=>'linked_product_id',
                    'additional'=>array('link_type_id'=>Mage_Catalog_Model_Product_Link::LINK_TYPE_GROUPED)
                    );
    }
}
