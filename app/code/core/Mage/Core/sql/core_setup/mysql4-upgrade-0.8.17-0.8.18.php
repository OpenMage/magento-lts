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

$installer->getConnection()->addColumn(
    $installer->getTable('core_email_variable'),
    'is_html',
    "tinyint(1) NOT NULL DEFAULT '0'",
);
$installer->getConnection()->changeColumn(
    $installer->getTable('core_email_variable_value'),
    'value',
    'value',
    'TEXT NOT NULL',
);

$installer->endSetup();
