<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogRule
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$ruleProductTable = $installer->getTable('catalogrule/rule_product');

$columnOptions = [
    'TYPE' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'LENGTH' => 32,
    'COMMENT' => 'Simple Action For Subitems',
];
$installer->getConnection()->addColumn($ruleProductTable, 'sub_simple_action', $columnOptions);

$columnOptions = [
    'TYPE' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
    'SCALE' => 4,
    'PRECISION' => 12,
    'NULLABLE' => false,
    'DEFAULT' => '0.0000',
    'COMMENT' => 'Discount Amount For Subitems',
];
$installer->getConnection()->addColumn($ruleProductTable, 'sub_discount_amount', $columnOptions);

$installer->endSetup();
