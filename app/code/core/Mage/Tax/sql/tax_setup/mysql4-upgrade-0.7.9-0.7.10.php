<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Tax
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Tax_Model_Resource_Setup $installer */
$installer  = $this;
$installer->startSetup();

$table = $installer->getTable('tax_calculation_rate');

$installer->getConnection()->addColumn($table, 'zip_is_range', 'TINYINT(1) DEFAULT NULL');
$installer->getConnection()->addColumn($table, 'zip_from', 'VARCHAR(10) DEFAULT NULL');
$installer->getConnection()->addColumn($table, 'zip_to', 'VARCHAR(10) DEFAULT NULL');

$installer->getConnection()->addKey($table, 'IDX_TAX_CALCULATION_RATE_RANGE', ['tax_calculation_rate_id', 'tax_country_id', 'tax_region_id', 'zip_is_range', 'tax_postcode']);

$installer->endSetup();
