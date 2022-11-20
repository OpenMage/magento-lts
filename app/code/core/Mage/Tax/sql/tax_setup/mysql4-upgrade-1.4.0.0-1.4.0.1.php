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
 * @package    Mage_Tax
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
    'unique'
);
