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
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

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
