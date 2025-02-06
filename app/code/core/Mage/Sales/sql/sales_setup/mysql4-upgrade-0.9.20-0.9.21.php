<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/** @var Mage_Sales_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn(
    $installer->getTable('sales_flat_quote'),
    'trigger_recollect',
    'tinyint(1) NOT NULL DEFAULT 0',
);
// no need to add attribute to the flat quote - it will be ignored

$installer->endSetup();
