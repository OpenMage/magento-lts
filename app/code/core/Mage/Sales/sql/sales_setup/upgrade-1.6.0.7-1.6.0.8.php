<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/** @var Mage_Sales_Model_Entity_Setup $installer */
$installer = $this;

$invoiceTable = $installer->getTable('sales/invoice');
$installer->getConnection()
    ->addColumn($invoiceTable, 'discount_description', [
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'    => 255,
        'comment'   => 'Discount Description',
    ]);

$creditmemoTable = $installer->getTable('sales/creditmemo');
$installer->getConnection()
    ->addColumn($creditmemoTable, 'discount_description', [
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'    => 255,
        'comment'   => 'Discount Description',
    ]);
