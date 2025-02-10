<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Sales_Model_Entity_Setup $installer
 */
$installer = $this;

$installer->getConnection()->addColumn(
    $installer->getTable('sales/invoice'),
    'base_total_refunded',
    'decimal(12,4) default NULL',
);
