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

$pageTable = $installer->getTable('cms/page');

$installer->getConnection()->addColumn(
    $pageTable,
    'custom_root_template',
    "VARCHAR(255) NOT NULL DEFAULT '' AFTER `custom_theme`",
);

$installer->getConnection()->addColumn(
    $pageTable,
    'custom_layout_update_xml',
    'TEXT NULL AFTER `custom_root_template`',
);

$installer->endSetup();
