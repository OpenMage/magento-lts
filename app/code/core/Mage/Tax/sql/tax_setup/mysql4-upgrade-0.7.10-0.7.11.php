<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Tax
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Tax_Model_Mysql4_Setup */

$installer->startSetup();

$installer->run("
    CREATE TABLE `{$installer->getTable('tax_order_aggregated_created')}` (
        `id`                    int(11) unsigned NOT NULL auto_increment,
        `period`                date NOT NULL DEFAULT '0000-00-00',
        `store_id`              smallint(5) unsigned NULL DEFAULT NULL,
        `code`                  varchar(255) NOT NULL default '',
        `order_status`          varchar(50) NOT NULL default '',
        `percent`               float(12,4) NOT NULL default '0.0000',
        `orders_count`          int(11) unsigned NOT NULL default '0',
        `tax_base_amount_sum`   float(12,4) NOT NULL default '0.0000',
        PRIMARY KEY  (`id`),
        UNIQUE KEY `UNQ_PERIOD_STORE_CODE_ORDER_STATUS` (`period`,`store_id`, `code`, `order_status`),
        KEY `IDX_STORE_ID` (`store_id`)
     ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->getConnection()->addConstraint(
    'FK_TAX_ORDER_AGGREGATED_CREATED_STORE',
    $this->getTable('tax_order_aggregated_created'), 
    'store_id',
    $this->getTable('core_store'), 
    'store_id'
);

$installer->endSetup();
