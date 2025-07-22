<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/** @var Mage_Sales_Model_Entity_Setup $installer */
$installer = $this;

foreach ([
    'sales/order', 'sales/order_grid', 'sales/creditmemo', 'sales/creditmemo_grid',
    'sales/invoice', 'sales/invoice_grid', 'sales/shipment','sales/shipment_grid',
] as $table
) {
    $tableName = $installer->getTable($table);
    $installer->getConnection()->dropKey($tableName, 'IDX_INCREMENT_ID');
    $installer->getConnection()->addKey($tableName, 'UNQ_INCREMENT_ID', 'increment_id', 'unique');
}
