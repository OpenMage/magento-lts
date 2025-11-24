<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/** @var Mage_Sales_Model_Entity_Setup $this */
$installer = $this;

$entitiesToAlter = [
    'quote_address',
    'order_address',
];

$attributes = [
    'vat_id' => ['type' => Varien_Db_Ddl_Table::TYPE_TEXT],
    'vat_is_valid' => ['type' => Varien_Db_Ddl_Table::TYPE_SMALLINT],
    'vat_request_id' => ['type' => Varien_Db_Ddl_Table::TYPE_TEXT],
    'vat_request_date' => ['type' => Varien_Db_Ddl_Table::TYPE_TEXT],
    'vat_request_success' => ['type' => Varien_Db_Ddl_Table::TYPE_SMALLINT],
];

foreach ($entitiesToAlter as $entityName) {
    foreach ($attributes as $attributeCode => $attributeParams) {
        $installer->addAttribute($entityName, $attributeCode, $attributeParams);
    }
}
