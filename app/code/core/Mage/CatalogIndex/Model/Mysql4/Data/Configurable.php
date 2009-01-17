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
 * @category   Mage
 * @package    Mage_CatalogIndex
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_CatalogIndex_Model_Mysql4_Data_Configurable extends Mage_CatalogIndex_Model_Mysql4_Data_Abstract
{
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
    protected function _prepareLinkFetchSelect($store, $table, $idField, $whereField, $id, $additionalWheres = array()) {
        $statusAttribute = Mage::getSingleton('eav/entity_attribute')->getIdByCode('catalog_product', 'status');

        $this->_getLinkSelect()
            ->joinLeft(array('s'=>$this->getTable('cataloginventory/stock_item')), "s.product_id=l.{$idField}", array())
            ->where('s.is_in_stock = 1')
            ->joinLeft(array('a'=>$this->getTable('catalog/product') . '_int'), "a.entity_id=l.{$idField} AND a.store_id = {$store} AND a.attribute_id = '{$statusAttribute}'", array())
            ->joinLeft(array('d'=>$this->getTable('catalog/product') . '_int'), "d.entity_id=l.{$idField} AND d.store_id = 0 AND d.attribute_id = '{$statusAttribute}'", array())
            ->where('a.value = 1 OR (a.value is null AND d.value = 1)');
    }
}