<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/** @var Mage_Sales_Model_Entity_Setup $installer */
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
