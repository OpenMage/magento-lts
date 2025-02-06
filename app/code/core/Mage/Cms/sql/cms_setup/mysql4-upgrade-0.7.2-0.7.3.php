<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
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
