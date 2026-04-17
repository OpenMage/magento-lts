<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/** @var Mage_Sales_Model_Entity_Setup $this */
$installer = $this;

$installer->getConnection()
    ->addColumn($installer->getTable('sales/order'), 'coupon_rule_name', [
        'TYPE'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'LENGTH'    => 255,
        'NULLABLE'  => true,
        'COMMENT'   => 'Coupon Sales Rule Name',
    ]);
