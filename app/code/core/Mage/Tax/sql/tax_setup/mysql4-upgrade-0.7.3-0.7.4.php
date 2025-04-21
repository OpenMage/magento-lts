<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Tax
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$table = $installer->getTable('tax_rate');
$installer->getConnection()->dropColumn($table, 'tax_county_id');
$installer->run("update {$table} set tax_postcode='*' where tax_postcode='' or tax_postcode is null");

$installer->endSetup();
