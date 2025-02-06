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

$installer->getConnection()->addColumn($installer->getTable('sales_order'), 'protect_code', 'VARCHAR( 6 ) NULL DEFAULT NULL');

$installer->addAttribute('order', 'protect_code', ['type' => 'static']);

$installer->run("UPDATE `{$installer->getTable('sales_order')}` SET protect_code = SUBSTRING(MD5(CONCAT(RAND(), DATE_FORMAT(NOW(), '%H %k %I %r %T %S'), RAND())), 5, 6) WHERE protect_code IS NULL");
