<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_CatalogRule
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$conn = $installer->getConnection();
$websites = $conn->fetchPairs("SELECT store_id, website_id FROM {$installer->getTable('core_store')}");
$ruleTable = $this->getTable('catalogrule');
if ($conn->tableColumnExists($ruleTable, 'store_ids')) {
    // catalogrule
    $conn->addColumn($ruleTable, 'website_ids', 'text');
    $select = $conn->select()
        ->from($ruleTable, ['rule_id', 'store_ids']);
    $rows = $conn->fetchAll($select);

    foreach ($rows as $r) {
        $websiteIds = [];
        foreach (explode(',', $r['store_ids']) as $storeId) {
            if (($storeId !== '') && isset($websites[$storeId])) {
                $websiteIds[$websites[$storeId]] = true;
            }
        }

        $conn->update($ruleTable, ['website_ids' => implode(',', array_keys($websiteIds))], "rule_id=" . $r['rule_id']);
    }
    $conn->dropColumn($ruleTable, 'store_ids');
}

// catalogrule_product
$ruleProductTable = $this->getTable('catalogrule_product');
if ($conn->tableColumnExists($ruleProductTable, 'store_id')) {
    $conn->addColumn($ruleProductTable, 'website_id', 'smallint unsigned not null');
    $unique = [];

    $select = $conn->select()
        ->from($ruleProductTable);
    $rows = $conn->fetchAll($select);

    //$q = $conn->query("select * from `$ruleProductTable`");
    foreach ($rows as $r) {
        $websiteId = $websites[$r['store_id']];
        $key = $r['from_time'] . '|' . $r['to_time'] . '|' . $websiteId . '|' . $r['customer_group_id'] . '|' . $r['product_id'] . '|' . $r['sort_order'];
        if (isset($unique[$key])) {
            $conn->delete($ruleProductTable, $conn->quoteInto("rule_product_id=?", $r['rule_product_id']));
        } else {
            $conn->update($ruleProductTable, ['website_id' => $websiteId], "rule_product_id=" . $r['rule_product_id']);
            $unique[$key] = true;
        }
    }
    $conn->dropKey($ruleProductTable, 'sort_order');
    $conn->raw_query("ALTER TABLE `$ruleProductTable` ADD UNIQUE KEY `sort_order` (`from_time`,`to_time`,`website_id`,`customer_group_id`,`product_id`,`sort_order`)");

    $conn->dropForeignKey($ruleProductTable, 'FK_catalogrule_product_store');
    $conn->dropColumn($ruleProductTable, 'store_id');

    $conn->dropForeignKey($ruleProductTable, 'FK_catalogrule_product_website');
    $conn->raw_query("ALTER TABLE `$ruleProductTable` ADD CONSTRAINT `FK_catalogrule_product_website` FOREIGN KEY (`website_id`) REFERENCES `{$this->getTable('core_website')}` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE");
}

// catalogrule_product_price
$ruleProductPriceTable = $this->getTable('catalogrule_product_price');
if ($conn->tableColumnExists($ruleProductPriceTable, 'store_id')) {
    $conn->addColumn($ruleProductPriceTable, 'website_id', 'smallint unsigned not null');
    $conn->delete($ruleProductPriceTable);

    $conn->dropKey($ruleProductPriceTable, 'rule_date');
    $conn->raw_query("ALTER TABLE `$ruleProductPriceTable` ADD UNIQUE KEY `rule_date` (`rule_date`,`website_id`,`customer_group_id`,`product_id`)");

    $conn->dropForeignKey($ruleProductPriceTable, 'FK_catalogrule_product_store');
    $conn->dropColumn($ruleProductPriceTable, 'store_id');

    $conn->dropForeignKey($ruleProductPriceTable, 'FK_catalogrule_product_price_website');
    $conn->raw_query("ALTER TABLE `$ruleProductPriceTable` ADD CONSTRAINT `FK_catalogrule_product_price_website` FOREIGN KEY (`website_id`) REFERENCES `{$this->getTable('core_website')}` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE");
}

$installer->endSetup();
