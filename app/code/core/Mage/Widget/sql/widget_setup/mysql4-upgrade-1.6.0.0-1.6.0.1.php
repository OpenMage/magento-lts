<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Widget
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->changeColumn(
    $installer->getTable('widget/widget_instance_page'),
    'page_group',
    'page_group',
    [
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'    => 255,
    ],
);

$installer->getConnection()->changeColumn(
    $installer->getTable('widget/widget_instance_page'),
    'page_for',
    'page_for',
    [
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'    => 255,
    ],
);
