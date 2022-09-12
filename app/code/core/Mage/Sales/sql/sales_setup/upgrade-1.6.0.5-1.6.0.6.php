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
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Sales_Model_Entity_Setup $installer */
$installer = $this;

$entitiesToAlter = [
    'quote_address',
    'order_address'
];

$attributes = [
    'vat_id' => ['type' => Varien_Db_Ddl_Table::TYPE_TEXT],
    'vat_is_valid' => ['type' => Varien_Db_Ddl_Table::TYPE_SMALLINT],
    'vat_request_id' => ['type' => Varien_Db_Ddl_Table::TYPE_TEXT],
    'vat_request_date' => ['type' => Varien_Db_Ddl_Table::TYPE_TEXT],
    'vat_request_success' => ['type' => Varien_Db_Ddl_Table::TYPE_SMALLINT]
];

foreach ($entitiesToAlter as $entityName) {
    foreach ($attributes as $attributeCode => $attributeParams) {
        $installer->addAttribute($entityName, $attributeCode, $attributeParams);
    }
}
