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
 * @copyright  Copyright (c) 2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Catalog_Model_Resource_Setup $installer */
$installer = $this;
$connection = $installer->getConnection();
$dbname = (string)Mage::getConfig()->getNode('global/resources/default_setup/connection/dbname');

$tableName = $installer->getTable('catalog_category_entity');
$engine = $connection->fetchOne("SELECT ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA=? AND TABLE_NAME=?", [
    $dbname,
    $tableName
]);
if (strtolower($engine) == "innodb") {
    $db->query("ALTER TABLE `$tableName` DROP INDEX `IDX_CATALOG_CATEGORY_ENTITY_PATH_ENTITY_ID`, ADD INDEX `IDX_CATALOG_CATEGORY_ENTITY_PATH_ENTITY_ID` (`path`)");
}

$tablesWithDuplicateIndexes = [
    "catalog_compare_item" => "IDX_CATALOG_COMPARE_ITEM_CUSTOMER_ID",
    "catalog_product_entity_datetime" => "IDX_CATALOG_PRODUCT_ENTITY_DATETIME_ENTITY_ID",
    "catalog_product_entity_decimal" => "IDX_CATALOG_PRODUCT_ENTITY_DECIMAL_ENTITY_ID",
    "catalog_product_entity_group_price" => "IDX_CATALOG_PRODUCT_ENTITY_GROUP_PRICE_ENTITY_ID",
    "catalog_product_entity_int" => "IDX_CATALOG_PRODUCT_ENTITY_INT_ENTITY_ID",
    "catalog_product_entity_text" => "IDX_CATALOG_PRODUCT_ENTITY_TEXT_ENTITY_ID",
    "catalog_product_entity_tier_price" => "IDX_CATALOG_PRODUCT_ENTITY_TIER_PRICE_ENTITY_ID",
    "catalog_product_entity_varchar" => "IDX_CATALOG_PRODUCT_ENTITY_VARCHAR_ENTITY_ID",
    "catalog_product_index_eav" => "IDX_CATALOG_PRODUCT_INDEX_EAV_ENTITY_ID",
    "catalog_product_index_eav_decimal" => "IDX_CATALOG_PRODUCT_INDEX_EAV_DECIMAL_ENTITY_ID",
    "catalog_product_index_eav_decimal_idx" => "IDX_CATALOG_PRODUCT_INDEX_EAV_DECIMAL_IDX_ENTITY_ID",
    "catalog_product_index_eav_idx" => "IDX_CATALOG_PRODUCT_INDEX_EAV_IDX_ENTITY_ID",
    "catalog_product_index_price" => "IDX_CATALOG_PRODUCT_INDEX_PRICE_WEBSITE_ID",
    "catalog_product_link" => "IDX_CATALOG_PRODUCT_LINK_LINK_TYPE_ID",
    "catalog_product_link_attribute_decimal" => "IDX_CAT_PRD_LNK_ATTR_DEC_PRD_LNK_ATTR_ID",
    "catalog_product_link_attribute_int" => "IDX_CATALOG_PRODUCT_LINK_ATTRIBUTE_INT_PRODUCT_LINK_ATTRIBUTE_ID",
    "catalog_product_link_attribute_varchar" => "IDX_CAT_PRD_LNK_ATTR_VCHR_PRD_LNK_ATTR_ID",
    "catalog_product_option_price" => "IDX_CATALOG_PRODUCT_OPTION_PRICE_OPTION_ID",
    "catalog_product_option_title" => "IDX_CATALOG_PRODUCT_OPTION_TITLE_OPTION_ID",
    "catalog_product_option_type_price" => "IDX_CATALOG_PRODUCT_OPTION_TYPE_PRICE_OPTION_TYPE_ID",
    "catalog_product_option_type_title" => "IDX_CATALOG_PRODUCT_OPTION_TYPE_TITLE_OPTION_TYPE_ID",
    "catalog_product_super_attribute" => "IDX_CATALOG_PRODUCT_SUPER_ATTRIBUTE_PRODUCT_ID",
    "catalog_product_super_attribute_label" => "IDX_CAT_PRD_SPR_ATTR_LBL_PRD_SPR_ATTR_ID",
    "catalog_product_super_attribute_pricing" => "IDX_CAT_PRD_SPR_ATTR_PRICING_PRD_SPR_ATTR_ID",
    "catalog_product_super_link" => "IDX_CATALOG_PRODUCT_SUPER_LINK_PRODUCT_ID"
];
foreach ($tablesWithDuplicateIndexes as $tableName=>$indexName) {
    $tableName = $installer->getTable($tableName);
    $engine = $connection->fetchOne("SELECT ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA=? AND TABLE_NAME=?", [
        $dbname,
        $tableName
    ]);
    if (strtolower($engine) == "innodb") {
        $db->query("ALTER TABLE `$tableName` DROP INDEX `$indexName`");
    }
}