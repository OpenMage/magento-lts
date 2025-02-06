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

foreach ([
    'sales/order', 'sales/order_grid', 'sales/creditmemo', 'sales/creditmemo_grid',
    'sales/invoice', 'sales/invoice_grid', 'sales/shipment','sales/shipment_grid',
] as $table
) {
    $tableName = $installer->getTable($table);
    $installer->getConnection()->dropKey($tableName, 'IDX_INCREMENT_ID');
    $installer->getConnection()->addKey($tableName, 'UNQ_INCREMENT_ID', 'increment_id', 'unique');
}
