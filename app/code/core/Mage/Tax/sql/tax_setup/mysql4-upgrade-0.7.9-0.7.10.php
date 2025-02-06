<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Tax
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
