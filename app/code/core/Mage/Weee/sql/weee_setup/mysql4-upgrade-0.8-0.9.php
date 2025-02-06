<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Weee
 */

/** @var Mage_Weee_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->changeColumn(
    $this->getTable('sales_flat_quote_item'),
    'weee_tax_applied',
    'weee_tax_applied',
    'text CHARACTER SET utf8',
);
$installer->getConnection()->changeColumn(
    $this->getTable('sales_flat_order_item'),
    'weee_tax_applied',
    'weee_tax_applied',
    'text CHARACTER SET utf8',
);

$installer->endSetup();
