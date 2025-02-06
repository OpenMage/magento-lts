<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Tax
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$table = $installer->getTable('tax_rate');
$installer->getConnection()->dropColumn($table, 'tax_county_id');
$installer->run("update {$table} set tax_postcode='*' where tax_postcode='' or tax_postcode is null");

$installer->endSetup();
