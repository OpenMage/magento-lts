<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Catalog_Model_Resource_Eav_Mysql4_Setup $installer */
$installer = $this;

$attributeId    = $installer->getAttributeId('catalog_category', 'is_active');
$entityTypeId   = $installer->getEntityTypeId('catalog_category');
$installer->updateAttribute('catalog_category', 'is_active', 'backend_type', 'int');

$categoryIntTable = $installer->getTable('catalog_category_entity_int');
$categoryTable = $installer->getTable('catalog_category_entity');
$attributesCount = $installer->getConnection()->fetchOne("SELECT count(*) FROM `{$categoryIntTable}` WHERE attribute_id='{$attributeId}'");
$valueId = $installer->getConnection()->fetchOne("SELECT MAX(value_id) FROM `{$categoryIntTable}`");
if (!$attributesCount) {
    $data = $installer->getConnection()->fetchAll("SELECT {$entityTypeId} as entity_type_id, {$attributeId} as attribute_id, 0 as store_id, entity_id, is_active as value FROM `{$categoryTable}`");
    foreach ($data as $row) {
        $row['value_id'] = ++$valueId;
        $data = $installer->getConnection()->insert($categoryIntTable, $row);
    }
}
