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
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/* @var $installer Mage_Sales_Model_Mysql4_Setup */
$installer = $this;

$orderEntityType = $installer->getEntityType('order');
$orderEntityTypeId = $orderEntityType['entity_type_id'];

$attributes = array(
    $installer->getAttribute($orderEntityTypeId, 'store_to_base_rate'),
    $installer->getAttribute($orderEntityTypeId, 'store_to_order_rate'),
    $installer->getAttribute($orderEntityTypeId, 'base_to_global_rate'),
    $installer->getAttribute($orderEntityTypeId, 'base_to_order_rate')
);

foreach ($attributes as $attribute) {
    $installer->getConnection()->addColumn($this->getTable('sales_order'), $attribute['attribute_code'], 'decimal(12,4) NULL');
}

try {
    $installer->getConnection()->beginTransaction();

    foreach ($attributes as $attribute) {
        $installer->run("
            UPDATE {$this->getTable('sales_order')} AS o, {$this->getTable('sales_order')}_{$attribute['backend_type']} AS od
            SET o.{$attribute['attribute_code']} = od.value
            WHERE od.entity_id = o.entity_id
                AND od.attribute_id = {$attribute['attribute_id']}
                AND od.entity_type_id = {$orderEntityTypeId}
        ");

        $installer->run("
            DELETE FROM {$this->getTable('sales_order')}_{$attribute['backend_type']}
            WHERE attribute_id = {$attribute['attribute_id']}
                AND entity_type_id = {$orderEntityTypeId}
        ");
    }

    foreach ($attributes as $attribute) {
        $installer->updateAttribute($orderEntityTypeId, $attribute['attribute_code'], array('backend_type' => 'static'));
    }

    $installer->getConnection()->commit();

} catch (Exception $e) {
    $installer->getConnection()->rollback();
    foreach ($attributes as $attribute) {
        $installer->getConnection()->dropColumn($this->getTable('sales_order'), $attribute['attribute_code']);
    }
    throw $e;
}
