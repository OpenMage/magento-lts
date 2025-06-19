<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/** @var Mage_Sales_Model_Resource_Setup $installer */
$installer = $this;

$installer->getConnection()->addColumn($installer->getTable('sales_order'), 'protect_code', 'VARCHAR( 6 ) NULL DEFAULT NULL');

$installer->addAttribute('order', 'protect_code', ['type' => 'static']);

$installer->run("UPDATE `{$installer->getTable('sales_order')}` SET protect_code = SUBSTRING(MD5(CONCAT(RAND(), DATE_FORMAT(NOW(), '%H %k %I %r %T %S'), RAND())), 5, 6) WHERE protect_code IS NULL");
