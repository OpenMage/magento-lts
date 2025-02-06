<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Admin
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

// Add reset password link token column
$installer->getConnection()->addColumn($installer->getTable('admin/user'), 'rp_token', [
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => 256,
    'nullable' => true,
    'default' => null,
    'comment' => 'Reset Password Link Token',
]);

// Add reset password link token creation date column
$installer->getConnection()->addColumn($installer->getTable('admin/user'), 'rp_token_created_at', [
    'type' => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
    'nullable' => true,
    'default' => null,
    'comment' => 'Reset Password Link Token Creation Date',
]);

$installer->endSetup();
