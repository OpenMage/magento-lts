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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Bundle product data retreiver
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Mage_Bundle_Model_CatalogIndex_Data_Bundle extends Mage_CatalogIndex_Model_Data_Simple
{

    /**
     * Defines when product type has children
     *
     * @var boolean
     */
    protected $_haveChildren = array(
                        Mage_CatalogIndex_Model_Retreiver::CHILDREN_FOR_TIERS=>false,
                        Mage_CatalogIndex_Model_Retreiver::CHILDREN_FOR_PRICES=>false,
                        Mage_CatalogIndex_Model_Retreiver::CHILDREN_FOR_ATTRIBUTES=>true,
                        );

    protected $_haveParents = false;

    /**
     * Retreive product type code
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
     * @return mixed
     */
    protected function _getLinkSettings()
    {
        return array(
            'table'=>'bundle/selection',
            'parent_field'=>'parent_product_id',
            'child_field'=>'product_id'
            );
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
    protected function _prepareLinkFetchSelect($store, $table, $idField, $whereField, $id, $additionalWheres = array())
    {
        $this->_addAttributeFilter($this->_getLinkSelect(), 'required_options', 'l', $idField, $store, 0);
    }
}
