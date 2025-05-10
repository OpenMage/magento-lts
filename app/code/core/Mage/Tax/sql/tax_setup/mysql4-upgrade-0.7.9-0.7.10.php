<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
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
