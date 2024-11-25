<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Cms
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
