<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('catalog/product'), 'required_options', 'tinyint(1) unsigned NOT NULL default 0');

$entityTypeId   = 'catalog_product';
$attributeId    = $installer->getAttributeId($entityTypeId, 'required_options');
$attributeTable = $installer->getAttributeTable($entityTypeId, $attributeId);

if ($attributeTable != $installer->getTable('catalog/product')) {
    $installer->run("
    UPDATE `{$installer->getTable('catalog/product')}` AS `p`
    INNER JOIN `{$attributeTable}` AS `a` ON `p`.`entity_id`=`a`.`entity_id`
        AND `a`.`attribute_id`={$attributeId} AND `a`.`store_id`=0
    SET `p`.`required_options` = `a`.`value`;
    ");

    $installer->updateAttribute($entityTypeId, $attributeId, 'backend_type', 'static');

    $installer->run("
    DELETE FROM `{$attributeTable}` WHERE `attribute_id`={$attributeId};
    ");
}

$installer->endSetup();
