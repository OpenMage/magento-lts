<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/** @var Mage_Sales_Model_Resource_Setup $installer */

$installer->startSetup();

$installer->run("
DELETE `{$installer->getTable('sales_flat_quote')}`.* FROM `{$installer->getTable('sales_flat_quote')}`
    LEFT JOIN `{$installer->getTable('core_store')}`
        ON `{$installer->getTable('sales_flat_quote')}`.`store_id`=`{$installer->getTable('core_store')}`.`store_id`
    WHERE `{$installer->getTable('core_store')}`.`store_id` IS NULL;
");

$installer->getConnection()->addConstraint(
    'FK_SALES_QUOTE_STORE',
    $installer->getTable('sales_flat_quote'),
    'store_id',
    $installer->getTable('core_store'),
    'store_id'
);

$installer->endSetup();
