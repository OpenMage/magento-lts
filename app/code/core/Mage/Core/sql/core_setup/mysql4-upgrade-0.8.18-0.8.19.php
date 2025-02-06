<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Core
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("
    ALTER TABLE `{$installer->getTable('core_email_variable')}` RENAME TO `{$installer->getTable('core/variable')}`;
    ALTER TABLE `{$installer->getTable('core_email_variable_value')}` RENAME TO `{$installer->getTable('core/variable_value')}`;
");

$installer->getConnection()->dropForeignKey($installer->getTable('core/variable_value'), 'FK_CORE_EMAIL_VARIABLE_VALUE_STORE_ID');
$installer->getConnection()->dropForeignKey($installer->getTable('core/variable_value'), 'FK_CORE_EMAIL_VARIABLE_VALUE_VARIABLE_ID');

$installer->getConnection()->addConstraint(
    'FK_CORE_VARIABLE_VALUE_STORE_ID',
    $installer->getTable('core/variable_value'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
);
$installer->getConnection()->addConstraint(
    'FK_CORE_VARIABLE_VALUE_VARIABLE_ID',
    $installer->getTable('core/variable_value'),
    'variable_id',
    $installer->getTable('core/variable'),
    'variable_id',
);

$installer->endSetup();
