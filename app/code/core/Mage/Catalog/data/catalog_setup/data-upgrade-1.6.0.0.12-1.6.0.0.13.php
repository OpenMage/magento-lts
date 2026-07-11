<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup $this */
$installer = $this;
$groupPriceAttrId = $installer->getAttribute('catalog_product', 'group_price', 'attribute_id');
$priceAttrId = $installer->getAttribute('catalog_product', 'price', 'attribute_id');

/** @var Varien_Db_Adapter_Pdo_Mysql $connection */
$connection = $installer->getConnection();

// update sort_order of Group Price attribute to be after Price
$select = $connection->select()
    ->join(
        ['t2' => $installer->getTable('eav/entity_attribute')],
        't1.attribute_group_id = t2.attribute_group_id',
        ['sort_order' => new Zend_Db_Expr('t2.sort_order + 1')],
    )->where('t1.attribute_id = ?', $groupPriceAttrId)
    ->where('t2.attribute_id = ?', $priceAttrId);
$query = $select->crossUpdateFromSelect(['t1' => $installer->getTable('eav/entity_attribute')]);
$connection->query($query);

// update sort_order of all other attributes to be after Group Price
$select = $connection->select()
    ->join(
        ['t2' => $installer->getTable('eav/entity_attribute')],
        't1.attribute_group_id = t2.attribute_group_id',
        ['sort_order' => new Zend_Db_Expr('t1.sort_order + 1')],
    )->where('t1.attribute_id != ?', $groupPriceAttrId)
    ->where('t1.sort_order >= t2.sort_order')
    ->where('t2.attribute_id = ?', $groupPriceAttrId);
$query = $select->crossUpdateFromSelect(['t1' => $installer->getTable('eav/entity_attribute')]);
$connection->query($query);
