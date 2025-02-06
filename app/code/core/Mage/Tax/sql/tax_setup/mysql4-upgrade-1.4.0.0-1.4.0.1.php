<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Tax
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

/**
 * Modify tax report aggregation table to have the tax percent field as part of unique key,
 * because tax rate can be changed (code will remain the same) and it will not be reflected in statistics
 * Data has to be truncated to avoid possible duplicates and then reaggregated to reflect the correct data
 */
$table = $installer->getTable('tax/tax_order_aggregated_created');
$installer->getConnection()->truncate($table);
$installer->getConnection()->addKey(
    $table,
    'UNQ_PERIOD_STORE_CODE_ORDER_STATUS',
    ['period', 'store_id', 'code', 'percent', 'order_status'],
    'unique',
);
