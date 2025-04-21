<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
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
