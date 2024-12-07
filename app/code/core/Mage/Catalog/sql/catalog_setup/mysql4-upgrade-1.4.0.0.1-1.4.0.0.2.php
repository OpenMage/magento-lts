<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->modifyColumn(
    $installer->getTable('catalog/category') . '_int',
    'value',
    'int(11) default NULL'
);
$installer->getConnection()->modifyColumn(
    $installer->getTable('catalog/category') . '_decimal',
    'value',
    'decimal(12,4) default NULL'
);
$installer->getConnection()->modifyColumn(
    $installer->getTable('catalog/category') . '_datetime',
    'value',
    'datetime default NULL'
);

$installer->endSetup();
