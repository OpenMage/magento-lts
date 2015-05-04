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
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();
$installer->run("
DROP TABLE IF EXISTS {$this->getTable('catalog_category_product_index')};
CREATE TABLE `{$installer->getTable('catalog_category_product_index')}` (
  `category_id` int(10) unsigned NOT NULL default '0',
  `product_id` int(10) unsigned NOT NULL default '0',
  `position` int(10) unsigned NOT NULL default '0',
  `is_parent` tinyint(1) unsigned NOT NULL default '0',
  UNIQUE KEY `UNQ_CATEGORY_PRODUCT` (`category_id`,`product_id`),
  KEY `IDX_CATEGORY_POSITION` (`category_id`,`position`),
  CONSTRAINT `FK_CATALOG_CATEGORY_PRODUCT_INDEX_PRODUCT_ENTITY` FOREIGN KEY (`product_id`) REFERENCES `{$installer->getTable('catalog_product_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CATALOG_CATEGORY_PRODUCT_INDEX_CATEGORY_ENTITY` FOREIGN KEY (`category_id`) REFERENCES `{$installer->getTable('catalog_category_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('catalog_product_enabled_index')};
CREATE TABLE `{$installer->getTable('catalog_product_enabled_index')}` (
  `product_id` int(10) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `visibility` smallint(5) unsigned NOT NULL default '0',
  UNIQUE KEY `UNQ_PRODUCT_STORE` (`product_id`,`store_id`),
  KEY `IDX_PRODUCT_VISIBILITY_IN_STORE` (`product_id`,`store_id`, `visibility`),
  CONSTRAINT `FK_CATALOG_PRODUCT_ENABLED_INDEX_PRODUCT_ENTITY` FOREIGN KEY (`product_id`) REFERENCES `{$installer->getTable('catalog_product_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CATALOG_PRODUCT_ENABLED_INDEX_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$installer->getTable('core_store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

/**
 * Build category product index
 */
$categories = $installer->getConnection()->fetchAll(
    $installer->getConnection()->select()
        ->from($installer->getTable('catalog_category_entity'))
        ->order('level')
        ->order('path')
);

// $installer->run("
// LOCK TABLES `{$installer->getTable('catalog_category_product_index')}` WRITE, `{$installer->getTable('catalog_category_product')}` READ, `{$installer->getTable('catalog_category_entity')}` READ;
// ");
$installer->run("
/*!40000 ALTER TABLE `{$installer->getTable('catalog_category_product_index')}` DISABLE KEYS */;
");

foreach ($categories as $category) {
    $categoryId = $category['entity_id'];
    $query = "INSERT INTO `{$installer->getTable('catalog_category_product_index')}`
    SELECT DISTINCT {$categoryId}, `product_id`, `position`, {$categoryId}=`category_id` AS `is_parent`
        FROM `{$installer->getTable('catalog_category_product')}`
        WHERE `category_id` IN(
            SELECT `entity_id` FROM `{$installer->getTable('catalog_category_entity')}`
                WHERE `path` LIKE '{$category['path']}%'
        )
        GROUP BY `product_id`
        ORDER BY `is_parent` DESC";
    $installer->run($query);
}

$installer->run("
/*!40000 ALTER TABLE `{$installer->getTable('catalog_category_product_index')}` ENABLE KEYS */;
");
// $installer->run("
// UNLOCK TABLES;
// ");

/**
 * Build product visibility index
 */
$statusAttributeId = $installer->getAttributeId('catalog_product', 'status');
$visibilityAttributeId = $installer->getAttributeId('catalog_product', 'visibility');

$installer->run("
/*!40000 ALTER TABLE `{$installer->getTable('catalog_product_enabled_index')}` DISABLE KEYS */;
");
$websiteIds = $installer->getConnection()->fetchCol("SELECT website_id FROM {$installer->getTable('core_website')}");
foreach ($websiteIds as $websiteId) {
    $storeIds = $installer->getConnection()->fetchCol("
        SELECT store_id FROM {$installer->getTable('core_store')} WHERE website_id={$websiteId}
    ");
    foreach ($storeIds as $storeId) {
        if (!$storeId) {
            continue;
        }
        $installer->run("
INSERT INTO {$installer->getTable('catalog_product_enabled_index')}
    SELECT t_v_default.entity_id, {$storeId}, IFNULL(t_v.value, t_v_default.value)
        FROM {$installer->getTable('catalog_product_entity_int')} AS t_v_default
        INNER JOIN {$installer->getTable('catalog_product_website')} AS w ON w.product_id=t_v_default.entity_id AND w.website_id={$websiteId}
        LEFT JOIN {$installer->getTable('catalog_product_entity_int')} AS `t_v`
            ON (t_v.entity_id = t_v_default.entity_id) AND (t_v.attribute_id='{$visibilityAttributeId}') AND (t_v.store_id='{$storeId}')
        INNER JOIN {$installer->getTable('catalog_product_entity_int')} AS `t_s_default`
            ON (t_s_default.entity_id = t_v_default.entity_id) AND (t_s_default.attribute_id='{$statusAttributeId}') AND t_s_default.store_id=0
        LEFT JOIN {$installer->getTable('catalog_product_entity_int')} AS `t_s`
            ON (t_s.entity_id = t_v_default.entity_id) AND (t_s.attribute_id='{$statusAttributeId}') AND (t_s.store_id='{$storeId}')
        WHERE
            t_v_default.attribute_id='{$visibilityAttributeId}' AND t_v_default.store_id=0
            AND (IFNULL(t_s.value, t_s_default.value)=".Mage_Catalog_Model_Product_Status::STATUS_ENABLED.")
        ");
    }
}

$installer->run("
/*!40000 ALTER TABLE `{$installer->getTable('catalog_product_enabled_index')}` ENABLE KEYS */
");

$installer->run("
ALTER TABLE `{$installer->getTable('core_url_rewrite')}` ADD INDEX `IDX_CATEGORY_REWRITE` (`category_id`, `is_system`, `product_id`, `store_id`, `id_path`);
");

$installer->endSetup();
