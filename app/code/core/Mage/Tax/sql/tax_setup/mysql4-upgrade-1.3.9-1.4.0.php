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

$installer->getConnection()->dropForeignKey(
    $installer->getTable('tax/sales_order_tax'),
    'FK_SALES_ORDER_TAX_ORDER',
);

$installer->endSetup();
