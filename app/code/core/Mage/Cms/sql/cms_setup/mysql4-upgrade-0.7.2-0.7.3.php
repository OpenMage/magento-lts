<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Cms
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$conn = $installer->getConnection();
$table = $installer->getTable('cms_page');

$conn->addColumn($table, 'custom_theme', 'varchar(100)');
$conn->addColumn($table, 'custom_theme_from', 'date');
$conn->addColumn($table, 'custom_theme_to', 'date');

$installer->endSetup();
