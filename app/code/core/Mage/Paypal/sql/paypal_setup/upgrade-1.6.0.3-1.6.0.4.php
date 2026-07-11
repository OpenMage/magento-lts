<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/** @var Mage_Paypal_Model_Resource_Setup $this */
$installer = $this;

$installer->getConnection()
    ->addColumn($installer->getTable('paypal/settlement_report_row'), 'store_id', [
        'type'    => Varien_Db_Ddl_Table::TYPE_TEXT,
        'comment' => 'Store ID',
        'length'  => '50']);
