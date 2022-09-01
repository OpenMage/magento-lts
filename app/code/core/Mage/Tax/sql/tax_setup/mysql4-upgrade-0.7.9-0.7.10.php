<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Mage
 * @package     Mage_Tax
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/** @var Mage_Tax_Model_Resource_Setup $installer */

$installer->startSetup();

$table = $installer->getTable('tax_calculation_rate');

$installer->getConnection()->addColumn($table, 'zip_is_range', "TINYINT(1) DEFAULT NULL");
$installer->getConnection()->addColumn($table, 'zip_from', "VARCHAR(10) DEFAULT NULL");
$installer->getConnection()->addColumn($table, 'zip_to', "VARCHAR(10) DEFAULT NULL");

$installer->getConnection()->addKey($table, 'IDX_TAX_CALCULATION_RATE_RANGE', ['tax_calculation_rate_id', 'tax_country_id', 'tax_region_id', 'zip_is_range', 'tax_postcode']);

$installer->endSetup();
