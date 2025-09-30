<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/** @var Mage_Customer_Model_Entity_Setup $installer */
$installer = $this;

$installer->getConnection()->addColumn($installer->getTable('customer/entity'), 'disable_auto_group_change', [
    'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
    'unsigned' => true,
    'nullable' => false,
    'default' => '0',
    'comment' => 'Disable automatic group change based on VAT ID',
]);
