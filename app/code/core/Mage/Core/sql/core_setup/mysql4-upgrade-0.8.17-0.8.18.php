<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
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
