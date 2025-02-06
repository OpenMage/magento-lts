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
