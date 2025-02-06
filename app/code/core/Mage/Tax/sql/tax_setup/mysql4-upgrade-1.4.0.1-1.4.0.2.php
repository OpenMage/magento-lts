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

$installer->getConnection()->changeColumn(
    $installer->getTable('tax/tax_calculation_rate'),
    'tax_postcode',
    'tax_postcode',
    'VARCHAR(21) NULL DEFAULT NULL',
);

$installer->endSetup();
